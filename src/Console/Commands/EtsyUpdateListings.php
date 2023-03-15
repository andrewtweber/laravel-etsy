<?php

namespace Etsy\Console\Commands;

use Etsy\Jobs\GetShopListings;
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
    protected $signature = 'etsy:shops {--force}';

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
        $shop_class = config('etsy.models.shop');

        $shops = $shop_class::whereNotNull('etsy_id')->cursor();

        $force = $this->option('force');

        foreach ($shops as $shop) {
            dispatch_with_delay(new GetShopListings($shop, $force), 5, 5);
        }
    }
}
