<?php

namespace App\Controllers;

use App\Models\CategoryModel;

class Categories extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new CategoryModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        $data['title'] = 'Categories';
        $data['categories'] = $this->model->findAll();
        return view('categories/index', $data);
    }

    public function create()
    {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success',
                'html'   => view('categories/form_modal')
            ]);
        }
        return redirect()->to('/categories');
    }

    public function store()
    {
        $rules = $this->model->validationRules;
        if (! $this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }
        $this->model->save($this->request->getPost());
        return $this->response->setJSON(['status' => 'success', 'message' => 'Category added']);
    }

    public function edit($id)
    {
        $category = $this->model->find($id);
        if (! $category) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Not found']);
        }
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success',
                'html'   => view('categories/form_modal', ['category' => $category])
            ]);
        }
    }

    public function update($id)
    {
        $rules = $this->model->validationRules;
        $rules['name'] = "required|min_length[3]|is_unique[categories.name,id,{$id}]";
        if (! $this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }
        $this->model->update($id, $this->request->getPost());
        return $this->response->setJSON(['status' => 'success', 'message' => 'Category updated']);
    }

    public function delete($id)
    {
        $this->model->delete($id);
        return redirect()->to('/categories')->with('message', 'Category deleted');
    }
}