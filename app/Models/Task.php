<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

/**
 * 
 *
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task query()
 * @mixin \Eloquent
 */
class Task extends Model
{

    protected $table = "gerenciamento_tarefas";

    protected $fillable = [
        "title",
        "description",
        "status",
        "priority",
        "user_id"
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
