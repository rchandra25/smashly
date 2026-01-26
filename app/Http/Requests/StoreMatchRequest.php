<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'game_session_id' => ['nullable', 'exists:game_sessions,id'],
            'opponent_id' => [
                'required',
                'exists:users,id',
                Rule::notIn([auth()->id()]),
            ],
            'player1_score' => ['required', 'integer', 'min:0', 'max:30'],
            'player2_score' => ['required', 'integer', 'min:0', 'max:30'],
        ];
    }

    public function messages(): array
    {
        return [
            'opponent_id.not_in' => 'You cannot play against yourself.',
            'player1_score.max' => 'Score cannot exceed 30 points.',
            'player2_score.max' => 'Score cannot exceed 30 points.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $score1 = (int) $this->input('player1_score');
            $score2 = (int) $this->input('player2_score');

            // Validate badminton scoring rules
            if (!$this->isValidBadmintonScore($score1, $score2)) {
                $validator->errors()->add('player1_score', 'Invalid badminton score. Games are played to 21, win by 2, max 30.');
            }
        });
    }

    private function isValidBadmintonScore(int $score1, int $score2): bool
    {
        $maxScore = max($score1, $score2);
        $minScore = min($score1, $score2);

        // Must have a winner
        if ($score1 === $score2) {
            return false;
        }

        // Normal game: winner has 21, loser has less than 20
        if ($maxScore === 21 && $minScore < 20) {
            return true;
        }

        // Deuce game: winner wins by 2, both scores between 20-29
        if ($maxScore >= 21 && $maxScore <= 29 && $maxScore - $minScore === 2) {
            return true;
        }

        // At 29-29, first to 30 wins
        if ($maxScore === 30 && $minScore === 29) {
            return true;
        }

        return false;
    }
}
