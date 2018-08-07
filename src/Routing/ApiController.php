<?php namespace Nano7\Http\Routing;

use Nano7\Http\Request;
use Nano7\Database\Model\Model;
use Nano7\Foundation\Support\Arr;

class ApiController extends Controller
{
    /**
     * @var string
     */
    protected $modelName;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var array
     */
    protected $exceptAttributesIn = [
        // in... index
        // in... show
    ];

    /**
     * @var array
     */
    protected $exceptSetAttributes = [
        '_id',
        'id',
        Model::CREATED_AT,
        Model::UPDATED_AT,
    ];

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $this->load()->query();

        $except = isset($this->exceptAttributesIn['index']) ? $this->exceptAttributesIn['index'] : [];

        // Tratar attributes
        $result = [];
        foreach ($query->get() as $model) {
            $result[] = Arr::except($model->toArray(), $except);
        }

        return response()->json($result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $model = $this->load();

        return response()->json($model);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Carregar objeto JSON
        $obj = $this->getJsonContent($request);
        if (is_null($obj)) {
            $this->error('JSON invalid', 500);
        }

        // Salvar
        $model = $this->setValues($obj);

        return response()->json($model);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $id = $this->getId($request);

        $model = $this->load($id);
        if (is_null($model)) {
            $this->error('Record not found', 404);
        }

        $except = isset($this->exceptAttributesIn['show']) ? $this->exceptAttributesIn['show'] : [];
        $attrs = Arr::except($model->toArray(), $except);

        return response()->json($attrs);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // Carregar ID
        $id = $this->getId($request);

        // Carregar objeto JSON
        $obj = $this->getJsonContent($request);
        if (is_null($obj)) {
            $this->error('JSON invalid', 500);
        }

        // Salvar
        $this->setValues($obj, $id);

        return response()->json((object)['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $count = 0;

        // Carregar IDs
        $ids = $this->getId($request);
        $ids = explode(',', $ids);
        foreach ($ids as $id) {
            $count += $this->deleteModel($id) ? 1 : 0;
        }

        return response()->json((object)['success' => ($count > 0), 'count' => $count]);
    }

    /**
     * Create model.
     * @return Model
     */
    protected function model($class = false)
    {
        if ($class !== false) {
            return app($class);
        }

        // Verificar se já foi criado
        if (! is_null($this->model)) {
            return $this->model;
        }

        return $this->model = app($this->modelName);
    }

    /**
     * @param bool $id
     * @return null|Model
     */
    protected function load($id = false)
    {
        // Carregar classe base
        $master = ($id === false) ? $this->model() : $model = $this->model()->query()->find($id);
        if (is_null($master)) {
            return null;
        }

        return $master;
    }

    /**
     * @param $values
     * @param bool $id
     * @return Model
     */
    protected function setValues($values, $id = false)
    {
        // Carregar classe master
        $master = ($id === false) ? $this->model() : $this->model()->query()->find($id);
        if (($id !== false) && (is_null($master))) {
            $this->error('Record not found', 404);
        }

        // Atribuir valores
        $this->setValuesInModel($master, $values);
        if ($master->hasChanged() || (($master->hasChanged() != true) && ($master->exists != true))) {
            $master->save();
        }

        return $master;
    }

    /**
     * @param Model $model
     * @param $values
     */
    protected function setValuesInModel(Model $model, $values)
    {
        // Atribuir valores ao model
        $array = (array) $values;
        $array = Arr::except($array, $this->exceptSetAttributes);

        foreach ($array as $key => $value) {
            $model->setAttribute($key, $value);
        }
    }

    /**
     * @param $id
     * @return bool|mixed|null
     */
    protected function deleteModel($id)
    {
        // Carregar classe master
        $master = $this->model()->query()->find($id);
        if (is_null($master)) {
            return false;
        }

        // Excluir master
        return $master->delete();
    }

    /**
     * @param Request $request
     * @return int|string|null
     */
    protected function getId(Request $request)
    {
        $id = $request->route()->param('id');
        if (is_null($id)) {
            $this->error('Invalid Id', 500);
        }

        return $id;
    }

    /**
     * @param Request $request
     * @return object
     */
    protected function getJsonContent(Request $request)
    {
        if ($request->isJson()) {
            return json_decode($request->getContent());
        }

        return (object) $request->all();
    }

    /**
     * @param $message
     * @param $code
     * @throws \Exception
     */
    protected function error($message, $code)
    {
        throw new \Exception($message, $code);
    }
}