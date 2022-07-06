<?php

namespace App\Services;

class ServiceBase
{
    public function defineModel($model)
    {
        $this->model = $model;

        return $this;
    }

    public function removeRegistro()
    {
        $registro = $this->model::find($this->id);

        $registro->delete();
    }
}