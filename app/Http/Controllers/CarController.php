<?php
namespace App\Http\Controllers;

use App\Car;
use App\Role;
use App\Permission;
use App\User;
use Illuminate\Http\Request;
use Elasticsearch\ClientBuilder;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use ONGR\ElasticsearchDSL\Search;
use ONGR\ElasticsearchDSL\Aggregation\Bucketing\DateHistogramAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Bucketing\TermsAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Metric\SumAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Pipeline\BucketSelectorAggregation;


class CarController extends Controller
{
    public function createCar(Request $request)
    {
        $car = Car::create($request->all());
        return response()->json($car);
    }

    public function updateCar(Request $request, $id)
    {
        $car = Car::find($id);
        $car->make = $request->input('make');
        $car->model = $request->input('model');
        $car->year = $request->input('year');
        $car->save();

        return response()->json($car);
    }

    public function deleteCar($id)
    {
        $car = Car::find($id);
        $car->delete();

        return response()->json('删除成功');
    }

    public function index(Request $request)
	{

		  $client = ClientBuilder::create()->build(); //elasticsearch-php client 
 $dateHistogramAggregation = new DateHistogramAggregation('articles_over_time', 'created_day', '1d');
 $termsAggregation = new TermsAggregation('field','user_name');
 $total_streaming = new SumAggregation('total_streaming','event_data.event_time');
 $scriptAggregation = new BucketSelectorAggregation(
    'streaming_bucket_filter',
    ['total_streaming' => 'total_streaming']
);
 $scriptAggregation->setScript(['lang'=>'expression','inline'=>'total_streaming >= 50']);
 $dateHistogramAggregation->addAggregation($total_streaming);
 $dateHistogramAggregation->addAggregation($scriptAggregation);
 $termsAggregation->addAggregation($dateHistogramAggregation);

$search = new Search();
$search->addAggregation($termsAggregation);

$count = $total_streaming->count($search);
print_r($count);
 
  $params = [
    'index' => 'bzs*',
	'type' => 'default',
    'body' => $search->toArray(),
  ];

//print_r($params);exit;  

  $results = $client->search($params);
  $resultsJson = json_encode($results);
  print_r($resultsJson);exit;	
	
		$client = ClientBuilder::create()->build();

		$params = [
			'index' => 'bzs*',
			'size' => 0,
			'type' => 'default',
			'body' => [
				'query' => [
					'match' => [
						'desc' => '消息'
					]
				]
			]
		];

		$results = $client->search($params);
		print_r($results);	


		$routeInfo = $request->url();
		print_r($routeInfo) ;exit;

		$admin = Role::where('name', '=', 'admin')->first();
		$user = User::where('username', '=', 'michele')->first();
		$createPost = Permission::where('name', '=', 'create-post')->first();

		echo $user->encan('edit-user');
		exit;

		$client = ClientBuilder::create()->build();

		$params = [
			'index' => 'customer',
			'type' => '',
			'body' => [
				'query' => [
					'match_all' => ''
				]
			]
		];

		$response = $client->search($params);
		return $response;
        //$cars = Car::all();
        //return response()->json($cars);
    }
}
