<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $table = 'notifications';
    // public function citoyen()
    // {
    //     return $this->belongsTo(Citoyen::class, 'id_cit');
    // }
    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'notifiable_id');
    // }
}
