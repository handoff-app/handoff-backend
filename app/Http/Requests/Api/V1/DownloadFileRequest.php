<?php

namespace App\Http\Requests\Api\V1;

use App\Contracts\Http\ResolvesToken;
use App\Entities\Auth\JWT\Scope;
use App\Services\Auth\TokenService;
use App\Traits\Http\ResolveToken;
use Illuminate\Foundation\Http\FormRequest;

class DownloadFileRequest extends FormRequest implements ResolvesToken
{
    use ResolveToken;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @param TokenService $tokenService
     * @return bool
     */
    public function authorize(TokenService $tokenService)
    {
        try {
            $token = $this->resolveToken();
        } catch (\Exception $e) {
            return false;
        }

        return !$tokenService->isRevoked($token)
            && $token->getSubject() === $this->route('fileUpload')->uuid
            && $token->hasScope(new Scope('FileUpload-view'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
