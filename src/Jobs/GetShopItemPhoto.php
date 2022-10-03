<?php

namespace Etsy\Jobs;

use Etsy\Models\ShopItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class GetShopItemPhoto
 *
 * @package App\Jobs
 */
class GetShopItemPhoto implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param ShopItem $item
     */
    public function __construct(
        public ShopItem $item
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $this->item->getPhotoFromEtsy();
    }
}
