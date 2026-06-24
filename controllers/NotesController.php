<?php

class NotesController
{

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAll($page = 1, $limit = 10, $search = null)
    {

        // Convertir en entiers
        $page = max(1, (int)$page);
        $limit = max(1, (int)$limit);

        // Calcul de l'offset
        $skip = ($page - 1) * $limit;

        // Filtre de recherche
        $filter = [];

        if ($search) {
            $filter = [
                '$or' => [
                    ['title'   => ['$regex' => $search, '$options' => 'i']],
                    ['content' => ['$regex' => $search, '$options' => 'i']]
                ]
            ];
        }

        // Récupération paginée + filtrée
        $cursor = $this->db->notes->find(
            $filter,
            [
                'skip'  => $skip,
                'limit' => $limit
            ]
        );
        $notes = $cursor->toArray();

        // Convertir tous les _id en string
        foreach ($notes as &$note) {
            $note['_id'] = (string) $note['_id'];
        }


        // Compter le total filtré
        $total = $this->db->notes->countDocuments($filter);

        echo json_encode([
            "page"  => $page,
            "limit" => $limit,
            "total" => $total,
            "pages" => ceil($total / $limit),
            "search" => $search,
            "data"  => $notes
        ]);
    }


    // GET /notes/:id
    public function getOne($id)
    {
        try {
            $objectId = new MongoDB\BSON\ObjectId($id);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid ID format"]);
            return;
        }

        $note = $this->db->notes->findOne(["_id" => $objectId]);

        if (!$note) {
            http_response_code(404);
            echo json_encode(["error" => "Note not found"]);
            return;
        }

        $note['_id'] = (string) $note['_id'];
        echo json_encode($note);
    }

    // POST /notes
    public function create($data)
    {
        $result = $this->db->notes->insertOne($data);

        echo json_encode([
            "_id" => (string) $result->getInsertedId(),
            "title" => $data["title"],
            "content" => $data["content"]
        ]);
    }

    // PUT /notes/:id
    public function update($id, $data)
    {
        try {
            $objectId = new MongoDB\BSON\ObjectId($id);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid ID format"]);
            return;
        }

        $updateResult = $this->db->notes->updateOne(
            ["_id" => $objectId],
            ['$set' => $data]
        );

        if ($updateResult->getMatchedCount() === 0) {
            http_response_code(404);
            echo json_encode(["error" => "Note not found"]);
            return;
        }

        $updatedNote = $this->db->notes->findOne(["_id" => $objectId]);
        $updatedNote['_id'] = (string) $updatedNote['_id'];

        echo json_encode($updatedNote);
    }

    // DELETE /notes/:id
    public function delete($id)
    {
        try {
            $objectId = new MongoDB\BSON\ObjectId($id);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid ID format"]);
            return;
        }

        $deleteResult = $this->db->notes->deleteOne(["_id" => $objectId]);

        if ($deleteResult->getDeletedCount() === 0) {
            http_response_code(404);
            echo json_encode(["error" => "Note not found"]);
            return;
        }

        echo json_encode(["success" => true]);
    }


public function patch($id, $data) {

    try {
        $objectId = new MongoDB\BSON\ObjectId($id);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid ID format"]);
        return;
    }

    // Mise à jour partielle
    $updateResult = $this->db->notes->updateOne(
        ["_id" => $objectId],
        ['$set' => $data]
    );

    if ($updateResult->getMatchedCount() === 0) {
        http_response_code(404);
        echo json_encode(["error" => "Note not found"]);
        return;
    }

    // Récupérer la note mise à jour
    $updatedNote = $this->db->notes->findOne(["_id" => $objectId]);
    $updatedNote['_id'] = (string) $updatedNote['_id'];

    echo json_encode($updatedNote);
  }

}