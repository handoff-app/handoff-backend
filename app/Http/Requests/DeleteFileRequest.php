<?php

namespace App\Http\Requests;

use App\Contracts\Http\ResolvesToken;
use App\Entities\Auth\JWT\Scope;
use App\Traits\Http\ResolveToken;
use Illuminate\Foundation\Http\FormRequest;

class DeleteFileRequest extends FormRequest implements ResolvesToken
{
    use ResolveToken;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $token = $this->resolveToken();

        return $token->getSubject() === $this->route('fileUpload')->uuid
            && $token->hasScope(new Scope('FileUpload-delete'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
