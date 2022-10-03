<?php

namespace Etsy\Console\Commands;

use Etsy\Etsy;
use Etsy\Models\EtsyTaxonomy;
use Illuminate\Console\Command;

/**
 * Class EtsyTaxonomies
 *
 * @package App\Console\Commands
 */
class EtsyTaxonomies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etsy:taxonomy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Etsy seller taxonomies';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $data = (new Etsy())->getTaxonomies();

        foreach ($data['results'] as $datum) {
            $this->process($datum);
        }
    }

    /**
     * @param array $datum
     */
    protected function process(array $datum)
    {
        $taxonomy = EtsyTaxonomy::firstOrNew([
            'etsy_taxonomy_id' => $datum['id'],
        ]);
        $taxonomy->name = $datum['name'];
        $taxonomy->etsy_parent_id = $datum['parent_id'];
        $taxonomy->save();

        if (count($datum['children']) > 0) {
            foreach ($datum['children'] as $child) {
                $this->process($child);
            }
        }
    }
}
