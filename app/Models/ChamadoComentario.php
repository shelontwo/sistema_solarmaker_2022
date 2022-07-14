<?php

namespace App\Models;

use App\Models\Integrador;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChamadoComentario extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'cco_id';

    protected $table = 'chamados_comentario';

    const UUID = 'uuid_cco_id';

    const CREATED_AT = 'cco_criado_em';

    const UPDATED_AT = 'cco_atualizado_em';

    const DELETED_AT = 'cco_deletado_em';

	protected $fillable = [
		'uuid_cco_id',
      	'cco_comentario',
        'fk_usu_id_usuario',
        'fk_cha_id_chamado'
	];

	protected static function boot()
    {
        parent::boot();

        static::creating(function (ChamadoComentario $chamadoComentario) {
            $chamadoComentario->uuid_cco_id = Str::uuid()->toString();
        });
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'fk_usu_id_usuario')->select('uuid_usu_id', 'usu_id', 'usu_nome');
    }

    public function chamado()
    {
        return $this->belongsTo(Chamado::class, 'fk_cha_id_chamado')->select('uuid_cha_id', 'cha_id', 'cha_descricao', 'cha_status');
    }
}
