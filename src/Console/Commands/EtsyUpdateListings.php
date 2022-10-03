<?php

namespace Etsy\Console\Commands;

use Etsy\Jobs\GetShopListings;
use Etsy\Models\Shop;
use Illuminate\Console\Command;

/**
 * Class EtsyUpdateListings
 *
 * @package App\Console\Commands
 */
class EtsyUpdateListings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etsy:shops';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch new and updated items from Etsy shops';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $shops = Shop::whereNotNull('etsy_id')->cursor();

        foreach ($shops as $shop) {
            dispatch_with_delay(new GetShopListings($shop), 5, 5);
        }
    }
}
