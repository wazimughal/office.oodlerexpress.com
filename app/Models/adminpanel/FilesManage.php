<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilesManage extends Model
{
    use HasFactory;
    protected $table='files';
    protected $primaryKey='id';
}
