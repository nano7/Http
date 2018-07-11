<?php namespace Nano7\Http\Routing;

use Nano7\Http\Request;

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
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json(['return' => 'index']);
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
        $id = $this->setValues($obj);

        return response()->json((object)['success' => true, 'id' => $id]);
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

        return response()->json($model);
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
     * @return int
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
        if ($master->isDirty() || (($master->isDirty() != true) && ($master->exists != true))) {
            $master->save();
        }

        // Atribuir valores das tabelas extends
        //foreach ($master->getExtends() as $extend) {
        //    $this->setValuesExtend($master, $extend, $values, $id);
        //}

        return $master->id;
    }

    /**
     * @param Model $model
     * @param $values
     */
    protected function setValuesInModel(Model $model, $values)
    {
        // Atribuir valores ao model
        $array = (array) $values;
        $array = array_except($array, $model->getExtends());

        foreach ($array as $key => $value) {
            $model->setAttribute($key, $value);
        }
    }

    /**
     * @param Model $master
     * @param $extend
     * @param $values
     * @param $id
     */
    protected function setValuesExtend(Model $master, $extend, $values, $id)
    {
        $values = (array) $values;

        // Verificar extend existe
        if (! array_key_exists($extend, $values)) {
            return;
        }

        // Verificar se deve exluir extend
        if ($values[$extend] === null) {
            $this->setValuesExtendDelete($master, $extend, $id);
            return;
        }

        $this->setValuesExtendCreateOrUpdate($master, $extend, $values, $id);
    }

    /**
     * @param Model $master
     * @param $extend
     * @param $values
     * @param $id
     */
    protected function setValuesExtendCreateOrUpdate(Model $master, $extend, $values, $id)
    {
        // Verificar se deve carregar extend model
        if ($id !== false) {
            $master->load($extend);
        }
        $eModel = $master->{$extend};

        // Se model for nulo, deve criar um novo
        if (is_null($eModel)) {
            $eModel = $master->$extend()->make([]);
        }

        // Atribuir valores do model extend
        $this->setValuesInModel($eModel, $values[$extend]);

        // Salvar model do extend
        $master->$extend()->save($eModel);
    }

    /**
     * @param Model $master
     * @param $extend
     * @param $id
     */
    protected function setValuesExtendDelete(Model $master, $extend, $id)
    {
        if ($id !== false) {
            $master->$extend()->delete();
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

        // Excluir registros que extendem
        foreach ($master->getExtends() as $extend) {
            $master->$extend()->delete();
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
        $id = $request->route()->parameter('id');
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
