<?php

namespace Etsy\Jobs;

use Etsy\Models\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class GetShopListings
 *
 * @package App\Jobs
 */
class GetShopListings implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param Shop $shop
     * @param bool $force
     */
    public function __construct(
        public Shop $shop,
        public bool $force = false
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $this->shop->getListingsFromEtsy($this->force);
    }
}
