<?php

namespace Etsy;

use Etsy\Models\Shop;
use Etsy\Models\ShopItem;
use Proste\SDK;

/**
 * Class Etsy
 *
 * @package Etsy
 */
class Etsy extends SDK
{
    public string $name = 'Etsy';

    public string $base_url = 'https://openapi.etsy.com/v3/';

    protected string $key;

    protected string $secret;

    /**
     * Etsy constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->key = config('services.etsy.key');
        $this->secret = config('services.etsy.secret');
    }

    /**
     * @return array
     */
    public function getTaxonomies(): array
    {
        return $this->get('/application/seller-taxonomy/nodes');
    }

    /**
     * Get shop listings. This does not return images
     *
     * @param Shop $shop
     *
     * @return array
     */
    public function getListings(Shop $shop): array
    {
        if (! $shop->etsy_id) {
            return [];
        }

        // Sample data
        //"listing_id" => 1062257066,
        //"user_id" => 22147373,
        //"shop_id" => 16362290,
        //"title" => "Dehydrated Duck Breast Treats For Cats, Dogs, & Ferrets",
        //"description" => "All natural human grade duck breast dehydrated over a span of 20 hours, then heat treated at 260 degrees to ensure no negative bacteria remain while maintaining key nutrients. Perfect as a treat for your little carnivore :)",
        //"state" => "active",
        //"creation_timestamp" => 1643071979,
        //"ending_timestamp" => 1653436379,
        //"original_creation_timestamp" => 1630293333,
        //"last_modified_timestamp" => 1643395248,
        //"state_timestamp" => 1640798361,
        //"quantity" => 2,
        //"shop_section_id" => 34610412,
        //"featured_rank" => -1,
        //"url" => "https://www.etsy.com/listing/1062257066/dehydrated-duck-breast-treats-for-cats",
        //"num_favorers" => 12,
        //"non_taxable" => false,
        //"is_customizable" => false,
        //"is_personalizable" => false,
        //"personalization_is_required" => false,
        //"personalization_char_count_max" => null,
        //"personalization_instructions" => null,
        //"listing_type" => "physical",
        //"tags" => [
        //  "healthy dog treats",
        //  "healthy pet treat",
        //  "ferret treats",
        //  "duck treat for cats",
        //  "dehydrated duck",
        //  "duck pet treat",
        //  "duck treat",
        //  "duck treat for dogs",
        //  "treats for ferrets",
        //  "healthy treats",
        //  "treats for dogs",
        //  "treats for cats",
        //  "pet treats",
        //],
        //"materials" => [
        //  "duck breast",
        //],
        //"shipping_profile_id" => 153004934533,
        //"processing_min" => 3,
        //"processing_max" => 5,
        //"who_made" => "i_did",
        //"when_made" => "made_to_order",
        //"is_supply" => false,
        //"item_weight" => 0,
        //"item_weight_unit" => null,
        //"item_length" => 0,
        //"item_width" => 0,
        //"item_height" => 0,
        //"item_dimensions_unit" => null,
        //"is_private" => false,
        //"style" => [],
        //"file_data" => "",
        //"has_variations" => false,
        //"should_auto_renew" => true,
        //"language" => "en-US",
        //"price" => [
        //  "amount" => 999,
        //  "divisor" => 100,
        //  "currency_code" => "USD",
        //],
        //"taxonomy_id" => 2924,
        //"production_partners" => [],
        //"skus" => [],

        return $this->get('/application/shops/' . $shop->etsy_id . '/listings/active', [
            'limit' => 100,
        ]);
    }

    /**
     * @param ShopItem $item
     *
     * @return array
     */
    public function getListingDetails(ShopItem $item): array
    {
        if (! $item->etsy_id) {
            return [];
        }

        // Sample data
        //"listing_id" => 1035073386
        //"listing_image_id" => 3195457010
        //"hex_code" => "A98B7A"
        //"red" => 169
        //"green" => 139
        //"blue" => 122
        //"hue" => 22
        //"saturation" => 27
        //"brightness" => 66
        //"is_black_and_white" => false
        //"creation_tsz" => 1625856572
        //"rank" => 1
        //"url_75x75" => "https://i.etsystatic.com/16362290/r/il/f3e62e/3195457010/il_75x75.3195457010_6bb7.jpg"
        //"url_170x135" => "https://i.etsystatic.com/16362290/r/il/f3e62e/3195457010/il_170x135.3195457010_6bb7.jpg"
        //"url_570xN" => "https://i.etsystatic.com/16362290/r/il/f3e62e/3195457010/il_570xN.3195457010_6bb7.jpg"
        //"url_fullxfull" => "https://i.etsystatic.com/16362290/r/il/f3e62e/3195457010/il_fullxfull.3195457010_6bb7.jpg"
        //"full_height" => 2000
        //"full_width" => 3000

        return $this->get('/application/listings/' . $item->etsy_id, [
            'includes' => [
                'images',
            ],
        ]);
    }

    /**
     * Find shop ID by name
     *
     * @param string $name
     *
     * @return array|null
     */
    public function getShopDetails(string $name): ?array
    {
        $results = $this->get('/application/shops', [
            'shop_name' => $name,
        ]);

        if ($results['count'] !== 1) {
            throw new \Exception("Results count is {$results['count']}");
        }

        // Sample data
        //"shop_id" => 16362290
        //"shop_name" => "clawspawsandraw"
        //"user_id" => 22147373
        //"create_date" => 1532377174
        //"title" => "Fresh & healthy homemade treats for your pets :)"
        //"announcement" => null
        //"currency_code" => "USD"
        //"is_vacation" => false
        //"vacation_message" => null
        //"sale_message" => "Thank you sooOooOoo much!! :) Hope you like it!!"
        //"digital_sale_message" => null
        //"update_date" => 1643087030
        //"listing_active_count" => 9
        //"digital_listing_count" => 0
        //"login_name" => "samanthapisillo"
        //"accepts_custom_requests" => false
        //"vacation_autoreply" => null
        //"url" => "https://www.etsy.com/shop/clawspawsandraw"
        //"image_url_760x100" => null
        //"num_favorers" => 125
        //"languages" => array:1 [
        //  0 => "en-US"
        //]
        //"icon_url_fullxfull" => "https://i.etsystatic.com/isla/ea1750/49248898/isla_fullxfull.49248898_qw219uhz.jpg?version=0"
        //"is_using_structured_policies" => false
        //"has_onboarded_structured_policies" => false
        //"include_dispute_form_link" => false
        //"is_direct_checkout_onboarded" => true
        //"is_etsy_payments_onboarded" => true
        //"is_opted_in_to_buyer_promise" => true
        //"is_calculated_eligible" => true
        //"is_shop_us_based" => true
        //"transaction_sold_count" => 121
        //"shipping_from_country_iso" => "US"
        //"shop_location_country_iso" => null
        //"policy_welcome" => null
        //"policy_payment" => null
        //"policy_shipping" => null
        //"policy_refunds" => null
        //"policy_additional" => null
        //"policy_seller_info" => null
        //"policy_update_date" => 0
        //"policy_has_private_receipt_info" => false
        //"has_unstructured_policies" => false
        //"policy_privacy" => null
        //"review_average" => 4.9744
        //"review_count" => 39

        return $results['results'][0] ?? null;
    }

    /**
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            'headers' => [
                'x-api-key' => $this->key,
            ],
        ];
    }
}
