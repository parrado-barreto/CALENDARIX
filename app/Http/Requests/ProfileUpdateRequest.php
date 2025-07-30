<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $this->user()->id],
            'dni' => ['nullable', 'string', 'max:30'],
            'celular' => ['nullable', 'string', 'max:20'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'pais' => ['nullable', 'string', 'max:100'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function persist(): void
    {
        $user = $this->user();
        $data = $this->validated();

        // Procesar imagen si existe
        if ($this->hasFile('foto')) {
            $archivo = $this->file('foto');
            $nombreArchivo = 'usuario_' . $user->id . '_' . time() . '.' . $archivo->getClientOriginalExtension();

            // Ruta destino fuera de Laravel storage
            $archivo->move('/home/u533926615/domains/calendarix.uy/public_html/images/perfiles/', $nombreArchivo);

            // Ruta pública completa
            $data['foto'] = 'https://calendarix.uy/images/perfiles/' . $nombreArchivo;
        }

        // Verifica si cambió el email
        if ($user->email !== $data['email']) {
            $user->email_verified_at = null;
        }

        $user->update($data);
    }
}
