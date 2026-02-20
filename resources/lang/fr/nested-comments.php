<?php

// translations for Hadialharbi/NestedComments
return [
    'comments' => [
        'general' => [
            'guest' => 'Visiteur',
            'no_comments_provided' => 'Aucun commentaire enregistré.',
            'no_commentable_record_set' => 'Aucun `Commentable record` défini.',
            'record_is_not_configured_for_reactions' => 'L\'enregistrement actuel n\'est pas configuré pour les réactions. Veuillez inclure le trait `HasReactions` au modèle.',
            'no_commentable_record_found_widget' => 'Aucun `Commentable record` trouvé. Merci de passer le model au widget',
            'reply' => 'Réponse',
            'no_replies' => 'Aucune réponse.',
            'comments' => 'Commentaires',
        ],
        'form' => [
            'field' => [
                'comment' => [
                    'label' => '',
                    // 'label' => 'Votre Commentaire',
                    'mention_items_placeholder' => 'Rechercher les utilisateurs par nom ou email',
                    'empty_mention_items_message' => 'Aucun utilisateur trouvé',

                ],
            ],
            'buttons' => [
                'submit' => 'Envoyer',
                'cancel' => 'Fermer',
                'add_comment' => 'Ajouter un nouveau commentaire',
                'add_reply' => 'Ajouter une réponse',
                'reply' => 'Répondre',
                'hide_replies' => 'Cacher les réponses',
                'refresh' => 'Rafraîchir',
            ],
        ],
        'table' => [
            'actions' => [
                'view_comments' => [
                    'heading' => 'Commentaires',
                    'close' => 'Fermer',
                ],
            ],
        ],
        'actions' => [
            'view_comment' => [
                'heading' => 'Voir les Commentaires',
                'close' => 'Fermer',
            ],
        ],
    ],
    'reactions' => [
        'add_reaction' => 'Ajouter une réaction',
    ],

];
