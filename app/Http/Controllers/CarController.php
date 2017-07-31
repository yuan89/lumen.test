<?php
namespace App\Http\Controllers;

use App\Car;
use App\Role;
use App\Permission;
use App\User;
use Illuminate\Http\Request;
use Elasticsearch\ClientBuilder;

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

    public function index()
	{

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
