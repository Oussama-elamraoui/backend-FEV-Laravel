<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enfant extends Model
{
    use HasFactory;
    protected $table = 'enfants';
    protected $fillable = [
        'FullName',
        'date_N',
        'identifiant',
    ];
    public function declarations()
    {
        return $this->hasMany(Declaration::class, 'id_Enf');
    }
}
