<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HanalMeditation extends Model
{
    protected $table = 'hanal_meditations';
    public $timestamps = false;

    protected $fillable = [
        'date', 'verse_ref', 'verse_text', 'title', 'hashtag', 'context_line',
        'body', 'prayer', 'tags', 'source', 'structure', 'opener', 'closer', 'theme_tags',
    ];

    protected $casts = [
        'date' => 'date',
        'body' => 'array',
        'tags' => 'array',
        'theme_tags' => 'array',
    ];

    /** 앱 today.json 스키마로 직렬화 (verse_text 포함 = 앱이 표시만). */
    public function toAppJson(): array
    {
        return [
            'verse_ref' => $this->verse_ref,
            'verse_text' => $this->verse_text,
            'title' => $this->title,
            'hashtag' => $this->hashtag,
            'context_line' => $this->context_line,
            'body' => $this->body,
            'prayer' => $this->prayer,
            'tags' => $this->tags,
        ];
    }
}