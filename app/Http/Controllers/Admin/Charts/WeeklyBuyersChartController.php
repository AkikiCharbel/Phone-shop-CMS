<?php

namespace App\Http\Controllers\Admin\Charts;

use App\Models\Brand;
use App\Models\BrandModel;
use App\Models\Phone;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\ChartController;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;
//use ConsoleTVs\Charts\Classes\Fusioncharts\Chart;

/**
 * Class WeeklyBuyersChartController
 * @package App\Http\Controllers\Admin\Charts
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class WeeklyBuyersChartController extends ChartController
{
    public function setup()
    {
        $this->chart = new Chart();

        // MANDATORY. Set the labels for the dataset points
        $labels = [];
        for ($days_backwards = 30; $days_backwards >= 0; $days_backwards--) {
            if ($days_backwards == 1) {
            }
            $labels[] = $days_backwards.' days ago';
        }
        $this->chart->labels($labels);

        // RECOMMENDED. Set URL that the ChartJS library should call, to get its data using AJAX.
        $this->chart->load(backpack_url('charts/weekly-buyers'));

        // OPTIONAL
         $this->chart->minimalist(false);
         $this->chart->displayLegend(true);
    }

    /**
     * Respond to AJAX calls with all the chart data points.
     *
     * @return json
     */
     public function data()
     {
         for ($days_backwards = 30; $days_backwards >= 0; $days_backwards--) {
             $phones[] = Phone::whereDate('created_at', today()
                 ->subDays($days_backwards))
                 ->count();
             $brandModel[] = BrandModel::whereDate('created_at', today()
                 ->subDays($days_backwards))
                 ->count();
             $brand[] = Brand::whereDate('created_at', today()
                 ->subDays($days_backwards))
                 ->count();
         }
dd($phones);
//         $this->chart->dataset('Users', 'line', $users)
//             ->color('rgb(77, 189, 116)')
//             ->backgroundColor('rgba(77, 189, 116, 0.4)');

         $this->chart->dataset('Phones', 'line', $phones)
             ->color('rgb(96, 92, 168)')
             ->backgroundColor('rgba(96, 92, 168, 0.4)');

         $this->chart->dataset('BrandModel', 'line', $brandModel)
             ->color('rgb(255, 193, 7)')
             ->backgroundColor('rgba(255, 193, 7, 0.4)');

         $this->chart->dataset('Brand', 'line', $brand)
             ->color('rgba(70, 127, 208, 1)')
             ->backgroundColor('rgba(70, 127, 208, 0.4)');
     }
}
