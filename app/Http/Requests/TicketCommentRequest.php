<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'comment' => ['required', 'string', 'min:2', 'max:5000'],
            'attachments' => ['sometimes', 'array', 'max:5'],
            'attachments.*' => [
                'file',
                'max:2048',
                'mimes:jpg,jpeg,png,pdf',
                'mimetypes:image/jpeg,image/png,application/pdf',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'comment.required' => 'Please enter a comment.',
            'comment.min' => 'A comment must be at least 2 characters.',
            'attachments.max' => 'You may attach a maximum of 5 files.',
            'attachments.*.file' => 'Each attachment must be a valid uploaded file.',
            'attachments.*.mimes' => 'Only JPG, PNG and PDF files are allowed.',
            'attachments.*.mimetypes' => 'Attachment file type is not permitted.',
            'attachments.*.max' => 'Each attachment must not exceed 2MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('comment')) {
            $this->merge([
                'comment' => trim(strip_tags((string) $this->input('comment'))),
            ]);
        }
    }
}
