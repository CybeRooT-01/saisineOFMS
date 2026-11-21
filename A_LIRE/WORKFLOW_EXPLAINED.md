# Parcours complet d'une demande

## Soumission Business
Le Business crée la demande  
Rôle validateur: Business  

## Qualification RJG
Le RJG analyse et qualifie la demande  
Rôle validateur: RJG  

## Validation Conformité
La Directrice Conformité valide  
Rôle validateur: Conformité  

## Étude DRJ
La DRJ étudie le dossier juridique  
Rôle validateur: DRJ  
Peut faire des validations partielles ou totales  

## Validation Business/Partenaire
Retour au Business pour validation interne  
Puis validation du partenaire externe  
Rôle validateur: Business  

## GO Final & Codification
GO formel + codification + hologramme  
Rôle validateur: DRJ ou RJG  

## Impression & Récupération
Impression et récupération du contrat  
Dernière étape (pas d'étape suivante)  

---

#  LES CONTRIBUTEURS - À Quoi Ça Sert?

## Contributeur (accès complet)
Peut VOIR la demande  
Peut MODIFIER la demande (titre, description, champs)  
Peut AJOUTER d'autres contributeurs  
Ne peut PAS valider (seuls les rôles validateurs peuvent valider)  

**Exemple d'usage:**  
La DRJ a besoin de l'avis de la Fiscalité sur un contrat. Elle ajoute quelqu'un de la Fiscalité comme contributeur. Cette personne peut alors voir le dossier, ajouter des commentaires, modifier des informations, mais ne peut pas valider l'étape.  

## Observateur (lecture seule)
Peut VOIR la demande  
Peut VOIR l'historique  
Ne peut PAS modifier  
Ne peut PAS valider  
Ne peut PAS ajouter d'autres contributeurs  

**Exemple d'usage:**  
Le RJG veut tenir informé un manager sans lui donner de pouvoir décisionnel. Il l'ajoute comme observateur.  

---

# 🎯 Qui Peut Ajouter des Contributeurs?

D'après le code (AuthorizationService):

- Le créateur de la demande  
- Les utilisateurs ayant le rôle validateur de l'étape actuelle  

**Exemple:**

- À l'étape "Étude DRJ", seul quelqu'un avec le rôle DRJ peut ajouter des contributeurs  
- À l'étape "Qualification RJG", seul quelqu'un avec le rôle RJG peut ajouter des contributeurs  

---

#  Résumé des Permissions

| Rôle                        | Voir | Modifier | Valider | Ajouter Contributeurs |
|----------------------------|------|----------|---------|------------------------|
| Créateur                   | ✅   | ✅       | ❌      | ✅                     |
| Validateur de l'étape      | ✅   | ✅       | ✅      | ✅                     |
| Contributeur               | ✅   | ✅       | ❌      | ✅                     |
| Observateur                | ✅   | ❌       | ❌      | ❌                     |
| Autre utilisateur          | ❌   | ❌       | ❌      | ❌                     |

---

# Cas d'Usage Concret

**Scénario: Une demande de contrat de partenariat**

- **Étape 1 - Soumission Business:** Marie (Business) crée la demande  
- **Étape 2 - Qualification RJG:** Ahmed (RJG) qualifie. Il ajoute Sophie (Conformité) comme observateur pour qu'elle soit au courant  
- **Étape 3 - Validation Conformité:** Sophie valide  
- **Étape 4 - Étude DRJ:** Jean (DRJ) étudie le dossier. Il a besoin de l'avis fiscal, donc il ajoute Thomas (Fiscalité) comme contributeur. Thomas peut maintenant voir le dossier, ajouter des commentaires, mais ne peut pas valider  
- **Étape 5 - Validation Business/Partenaire:** Retour à Marie qui valide  
- **Étape 6 - GO Final:** Ahmed (RJG) donne le GO final  
- **Étape 7 - Impression:** Dernière étape, le contrat est prêt  

Tout au long du processus, Sophie (observateur) peut suivre l'évolution sans pouvoir modifier quoi que ce soit.