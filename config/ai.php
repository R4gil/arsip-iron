<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Provider
    |--------------------------------------------------------------------------
    |
    | Provider AI yang digunakan: openai, gemini, ollama
    |
    */
    'provider' => env('AI_PROVIDER', 'ollama'),

    /*
    |--------------------------------------------------------------------------
    | OpenAI Configuration
    |--------------------------------------------------------------------------
    */
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-3.5-turbo'),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 1000),
        'temperature' => env('OPENAI_TEMPERATURE', 0.7),
    ],

    /*
    |--------------------------------------------------------------------------
    | Gemini Configuration
    |--------------------------------------------------------------------------
    */
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-pro'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Ollama Configuration
    |--------------------------------------------------------------------------
    */
    'ollama' => [
        'base_url' => env('OLLAMA_URL', 'http://127.0.0.1:11434'),
        'model' => env('OLLAMA_MODEL', 'gemma3:4b'),
        'max_tokens' => env('OLLAMA_MAX_TOKENS', 2000),
        'temperature' => env('AI_TEMPERATURE', 0.3),
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Settings
    |--------------------------------------------------------------------------
    */
    'settings' => [
        'max_archives_for_context' => 5,
        'max_context_length' => 10000,
        'timeout' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Search Strategy
    |--------------------------------------------------------------------------
    |
    | Strategy untuk pencarian arsip: keyword, embedding
    | - keyword: Pencarian berbasis kata kunci dengan scoring relevansi
    | - embedding: Pencarian berbasis vector similarity (RAG)
    |
    */
    'search_strategy' => env('AI_SEARCH_STRATEGY', 'embedding'),

    /*
    |--------------------------------------------------------------------------
    | Tesseract OCR Path
    |--------------------------------------------------------------------------
    */
    'tesseract_path' => env('TESSERACT_PATH', 'C:\Program Files\Tesseract-OCR\tesseract.exe'),
];
