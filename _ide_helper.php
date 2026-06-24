<?php

/*
 Pour reconnaître certaines classes fournies par des extensions PHP écrites
 en C, comme l’extension MongoDB.
 Les classes comme MongoDB\BSON\ObjectId existent bien dans PHP,
 mais elles ne sont pas visibles dans le code source.
 L’IDE ne peut donc pas les indexer, et les souligne en rouge.
 En créant ici une "fausse" définition de la classe, l’IDE comprend
 qu’elle existe et arrête de signaler une erreur
 */

namespace MongoDB\BSON;

// Définition factice pour aider l’IDE
class ObjectId {} 
