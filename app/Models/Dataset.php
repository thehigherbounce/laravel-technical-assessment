<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dataset extends Model
{
    protected $table = 'datasets';
    protected $fillable = ['category', 'firstname', 'lastname', 'email', 'gender', 'birthdate'];
    public $timestamps = true;
    public function scopeFilter($query, $params)
    {
        if (!empty($params['category'])) {
            $query->where('category', 'like', $params['category']);
        }
        if (!empty($params['gender'])) {
            $query->where('gender', '=', $params['gender']);
        }
        if (!empty($params['age'])) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) = ?', $params['age']);
        }
        if (!empty($params['birthday'])) {
            $query->where('birthdate', '=', $params['birthday']);
        }
        if (!empty($params['age_range'])) {
            $age_range = explode('-', $params['age_range']);
            $min_age = (int) $age_range[0];
            $max_age = (int) $age_range[1];
            $query->whereRaw('TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN ? AND ?', [$min_age, $max_age]);
        }
        return $query;
    }
}
