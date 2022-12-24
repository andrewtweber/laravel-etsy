<?php

namespace Etsy\Observers;

use Etsy\Etsy;
use Etsy\Jobs\GetShopListings;
use Etsy\Models\Shop;
use Proste\Exceptions\HttpException;
use Snaccs\Support\Url;

/**
 * Class ShopObserver
 *
 * @package Etsy\Observers
 */
class ShopObserver
{
    /**
     * TODO: dispatch this in a job
     *
     * @param Shop $shop
     */
    public function saving(Shop $shop)
    {
        if ($shop->etsy_id) {
            return;
        }

        $url = new Url($shop->website);

        if ($url->base_domain !== 'etsy.com') {
            return;
        }

        // Path will be /shop/{name} or /{country}/shop/{name} or {name}.etsy.com
        if ($url->subdomain) {
            $name = $url->subdomain;
        } else {
            $path = parse_url($shop->website, PHP_URL_PATH);
            $parts = explode('/', trim($path, '/'));

            if (count($parts) < 2 || count($parts) > 3) {
                return;
            }

            $name = array_pop($parts);
            $validate = array_pop($parts);

            if ($validate !== 'shop') {
                return;
            }
        }

        try {
            $data = (new Etsy())->getShopDetails($name);

            if (isset($data['shop_id'])) {
                $shop->etsy_id = $data['shop_id'];
            }
        } catch (HttpException $e) {
            // TODO: log?
        }
    }

    /**
     * @param Shop $shop
     */
    public function created(Shop $shop)
    {
        if ($shop->etsy_id) {
            dispatch_with_delay(new GetShopListings($shop), 5, 5);
        }
    }
}
