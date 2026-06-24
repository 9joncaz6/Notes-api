<?php

class NoteValidator
{

    public static function validate($input)
    {

        if (!isset($input['title']) || !isset($input['content'])) {
            return ["error" => "Missing title or content"];
        }

        $title = trim(strip_tags($input['title']));
        $content = trim(strip_tags($input['content']));

        if (strlen($title) < 1 || strlen($title) > 200) {
            return ["error" => "Title must be between 1 and 200 characters"];
        }

        if (strlen($content) < 1 || strlen($content) > 5000) {
            return ["error" => "Content must be between 1 and 5000 characters"];
        }

        return [
            "title" => $title,
            "content" => $content
        ];
    }


    public static function validatePartial($input)
    {

        $update = [];

        // Si title est envoyé → on le valide
        if (isset($input['title'])) {
            $title = trim(strip_tags($input['title']));

            if (strlen($title) < 1 || strlen($title) > 200) {
                return ["error" => "Title must be between 1 and 200 characters"];
            }

            $update['title'] = $title;
        }

        // Si content est envoyé → on le valide
        if (isset($input['content'])) {
            $content = trim(strip_tags($input['content']));

            if (strlen($content) < 1 || strlen($content) > 5000) {
                return ["error" => "Content must be between 1 and 5000 characters"];
            }

            $update['content'] = $content;
        }

        // Si aucun champ n'est envoyé
        if (empty($update)) {
            return ["error" => "No valid fields provided"];
        }

        return $update;
    }
}
