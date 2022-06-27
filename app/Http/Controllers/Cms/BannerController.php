<?php

namespace App\Http\Controllers\Cms;

use App\Banner;
use App\Http\Controllers\Cms\RestrictedController;
use App\Traits\UploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BannerController extends RestrictedController
{
  use UploadTrait;

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $data = $request->all();
    #PAGE TITLE E BREADCRUMBS
    $headers = parent::headers(
      "Banners",
      [
        [
          "icon" => "",
          "title" => "Banner",
          "url" => "",
        ],
      ]
    );
    #LISTA DE ITENS
    $titles = json_encode(["#", "Status", "Pop-up", "Imagem", "Imagem Mobile"]);
    $actions = json_encode([
      [
        'path' => '{item}/edit',
        'icon' => 'fa fa-pencil',
        'label' => 'Editar',
        'color' => 'primary',
      ],
    ]);

    $busca = '';
    $pagination = 15;
    if (!empty($data['busca'])) {
      if ($data['busca'] != null && $data['busca'] != '') {
        $busca = $data['busca'];
      }
      $pagination = 500;
    }
    $items = Banner::select('id', 'active', 'popup', 'image', 'mobile_image')
      ->where(function ($query) use ($data) {
        if (!empty($data['busca'])) {
          $query->where('image', 'LIKE', "%" . $data['busca'] . "%");
        }
      })
      ->orWhere(function ($query) use ($data) {
        if (!empty($data['busca'])) {
          $query->where('active', 'LIKE', "%" . $data['busca'] . "%");
        }
      })
      ->orWhere(function ($query) use ($data) {
        if (!empty($data['busca'])) {
          $query->where('mobile_image', 'LIKE', "%" . $data['busca'] . "%");
        }
      })
      ->orderBy('turn', 'asc')
      ->paginate($pagination);

    foreach ($items as $key => $value) {
      if (!empty($items[$key]->image)) {
        $items[$key]->image = [
          "type" => 'img',
          "src" => asset($value->image)
        ];
      } else {
        $items[$key]->image = 'Não possui';
      }
    }

    foreach ($items as $key => $value) {
      if (!empty($items[$key]->mobile_image)) {
        $items[$key]->mobile_image = [
          "type" => 'img',
          "src" => asset($value->mobile_image)
        ];
      } else {
        $items[$key]->mobile_image = 'Não possui';
      }
    }

    foreach ($items as $item) {
      $item['active'] = [
        'type' => 'badge',
        'status' => $item['active'] == 1 ? 'success' : 'danger',
        'text' => $item['active'] == 1 ? 'Ativo' : 'Inativo'
      ];
      $item['popup'] = [
        'type' => 'badge',
        'status' => $item['popup'] == 1 ? 'info' : 'warning',
        'text' => $item['popup'] == 1 ? 'Sim' : 'Não'
      ];
    }

    return view('cms.banner.index', compact('headers', 'titles', 'items', 'busca', 'actions'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $data = $request->all();
    if (!isset($data['active'])) {
      $data['active'] = 0;
    }

    if (!isset($data['popup'])) {
      $data['popup'] = 0;
    }

    $validation = $this->validation($data, 'store');
    if ($validation->fails()) {
      return redirect()->back()->withErrors($validation)->withInput();
    }

    if (empty($data['image']) && empty($data['mobile_image'])) {
      return redirect()->back()->withErrors(['images' => 'Pelo menos um campo de imagem deve ser preenchido'])->withInput();
    } else {
      if (!empty($data['image'])) {
        if (!$image = $this->uploadValidFile('banner', $data['image'], 1920)) {
          return redirect()->back()->withErrors(['image' => 'A image não pode ser carregada'])->withInput();
        }
      }
      if (!empty($data['mobile_image'])) {
        if (!$mobile_image = $this->uploadValidFile('banner', $data['mobile_image'], 1920)) {
          return redirect()->back()->withErrors(['mobile_image' => 'A image não pode ser carregada'])->withInput();
        }
      }
    }

    $data['image'] = !empty($image) ? $image : '';
    $data['mobile_image'] = !empty($mobile_image) ? $mobile_image : '';

    $newBanner = Banner::create($data);
    $newBanner->image = asset($newBanner->image);
    $newBanner->mobile_image = asset($newBanner->mobile_image);

    return redirect()->back()->with('message', 'Registro cadastrado com sucesso!');
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Banner  $banner
   * @return \Illuminate\Http\Response
   */
  public function edit(Banner $banner)
  {
    #PAGE TITLE E BREADCRUMBS
    $headers = parent::headers(
      "banner",
      [
        ["icon" => "", "title" => "Banner", "url" => route('banner.index')],
        ["icon" => "", "title" => "Editar", "url" => ""],
      ]
    );

    if (empty($banner)) {
      return redirect()->back();
    }
    return view('cms.banner.edit', compact('headers', 'banner'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Banner  $banner
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Banner $banner)
  {
    $data = $request->all();
    $image_up = '';
    $mobile_image_up = '';

    if (!isset($data['active'])) {
      $data['active'] = 0;
    }

    if (!isset($data['popup'])) {
      $data['popup'] = 0;
    }

    $validation = $this->validation($data, 'update');
    if ($validation->fails()) {
      return redirect()->back()->withErrors($validation)->withInput();
    }

    if (!array_key_exists('image', $data)) {
      $data['image'] = $banner->image;
    }
    if (gettype($data['image']) != 'string') {
      if (!empty($data['image'])) {
        if (!$image_up = $this->uploadValidFile('banner', $data['image'], 1920)) {
          return redirect()->back()->withErrors(['image' => 'A image não pode ser carregada'])->withInput();
        }
        if (!empty($banner->image)) {
          unlink($banner->image);
          if ($banner->image == $banner->mobile_image) {
            $banner->mobile_image = "";
          };
        }
      }
    } else {
      $image_up = $banner->image;
    }

    if (!array_key_exists('mobile_image', $data)) {
      $data['mobile_image'] = $banner->mobile_image;
    }
    if (gettype($data['mobile_image']) != 'string') {
      if (!empty($data['mobile_image'])) {
        if (!$mobile_image_up = $this->uploadValidFile('banner', $data['mobile_image'], 1920)) {
          return redirect()->back()->withErrors(['mobile_image' => 'A image não pode ser carregada'])->withInput();
        }
        if (!empty($banner->mobile_image)) {
          unlink($banner->mobile_image);
        }
      }
    } else {
      $mobile_image_up = $banner->mobile_image;
    }

    $data['image'] = $image_up;
    $data['mobile_image'] = $mobile_image_up;

    $banner->update($data);

    $banner->image = asset($banner->image);
    $banner->mobile_image = asset($banner->mobile_image);

    return redirect()->route('banner.index')->with('message', 'Registro atualizado com sucesso!');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Banner  $banner
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request)
  {
    $data = $request->all();

    if (isset($data['registro'])) {
      $banner = Banner::whereIn('id', $data['registro'])->get();
      foreach ($banner as $banner) {
        if (!empty($banner->image)) {
          unlink($banner->image);
          if ($banner->image == $banner->mobile_image) {
            $banner->mobile_image = "";
          };
        }
        if (!empty($banner->mobile_image)) {
          unlink($banner->mobile_image);
        }
        $banner->delete();
      }      
      return redirect()->back()->with('message', 'Itens excluídos com sucesso!');
    } else {
      return redirect()->back()->with('message', 'Itens excluídos com sucesso!');
    }
  }

  public function messages()
  {
    return [
      'validation.image' => 'A imagem não pode ser carregada'
    ];
  }

  private function validation(array $data, $funtction)
  {
    $validator = [
      'image' => 'nullable|image',
      'link' => 'nullable|string',
      'active' => 'nullable|boolean',
      'mobile_image' => 'nullable|image',
      'title' => 'nullable|string|max:30',
      'description' => 'nullable|string',
      'button' => 'nullable|string|max:20',
      'turn' => 'required|integer'
    ];

    $messages = [
      'title.max' => 'O número máximo de caracteres é de 30',
      'image' => 'A imagem não pode ser carregada',
      'image.required' => 'Pelo menos um dos campos de imagem precisa ser preenchido',
      'mobile_image' => 'A imagem não pode ser carregada',
      'mobile_image.required' => 'Pelo menos um dos campos de imagem precisa ser preenchido',
      'description.required' => 'É necessário preencher a descrição',
      'button.max' => 'O número máximo de caracteres é de 20',
      'turn.required' => 'É necessário escolher uma ordem'
    ];

    $data['description'] = strip_tags($data['description']) != '' ? $data['description'] : '';

    if ($data['button'] != '') {
      $validator['link'] = 'required|string';
      $messages['link.required'] = 'Para um cadastrar um botão, é necessário um título E link';
    }
    if ($data['link'] != '') {
      $validator['button'] = 'required|string';
      $messages['button.required'] = 'Para um cadastrar um botão, é necessário um link E título';
    }

    $valid = Validator::make($data, $validator, $messages);

    if ($funtction != 'update') {
      $valid->sometimes('mobile_image', 'required', function ($input) {
        if (empty($input->image) || $input->image == '' || $input->image == null) {
          return true;
        }
      });
      $valid->sometimes('image', 'required', function ($input) {
        if (empty($input->mobile_image) || $input->mobile_image == '' || $input->mobile_image == null) {
          return true;
        }
      });
    }

    return $valid;
  }
}
