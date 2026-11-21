<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $saisine->reference_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #ff5500;
        }
        .header h1 {
            color: #ff5500;
            font-size: 24px;
            margin: 0 0 10px 0;
        }
        .header .reference {
            font-size: 16px;
            color: #666;
            font-weight: bold;
        }
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .section-title {
            background-color: #ff5500;
            color: white;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
            padding: 5px 0;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
            color: #555;
        }
        .info-value {
            flex: 1;
            color: #333;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-critical {
            background-color: #fee;
            color: #c00;
        }
        .badge-priority {
            background-color: #fff3e0;
            color: #ff5500;
        }
        .badge-standard {
            background-color: #f5f5f5;
            color: #666;
        }
        .badge-stage {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        .description-box {
            background-color: #f9f9f9;
            border-left: 4px solid #ff5500;
            padding: 12px;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table th {
            background-color: #f5f5f5;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #ddd;
        }
        table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Demande de Saisine</h1>
        <div class="reference">{{ $saisine->reference_number }}</div>
    </div>

    <!-- Basic Information -->
    <div class="section">
        <div class="section-title">Informations générales</div>
        <div class="info-row">
            <div class="info-label">Titre:</div>
            <div class="info-value">{{ $saisine->title }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Type de demande:</div>
            <div class="info-value">{{ $saisine->requestType->display_name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Créé par:</div>
            <div class="info-value">{{ $saisine->creator->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Date de création:</div>
            <div class="info-value">{{ $saisine->created_at->format('d/m/Y H:i') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Étape actuelle:</div>
            <div class="info-value">
                <span class="badge badge-stage">{{ $saisine->currentStage->display_name }}</span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Urgence:</div>
            <div class="info-value">
                @if($saisine->urgency_level === 'critical')
                    <span class="badge badge-critical">Critique</span>
                @elseif($saisine->urgency_level === 'priority')
                    <span class="badge badge-priority">Prioritaire</span>
                @else
                    <span class="badge badge-standard">Standard</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Description -->
    <div class="section">
        <div class="section-title">Description</div>
        <div class="description-box">
            {{ $saisine->description }}
        </div>
    </div>

    <!-- Custom Fields -->
    @if(!empty($saisine->custom_fields))
    <div class="section">
        <div class="section-title">Champs personnalisés</div>
        @foreach($saisine->custom_fields as $key => $value)
        <div class="info-row">
            <div class="info-label">{{ ucfirst(str_replace('_', ' ', $key)) }}:</div>
            <div class="info-value">{{ $value }}</div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Contributors -->
    @if($saisine->contributors->count() > 0)
    <div class="section">
        <div class="section-title">Contributeurs</div>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Structure</th>
                </tr>
            </thead>
            <tbody>
                @foreach($saisine->contributors as $contributor)
                <tr>
                    <td>{{ $contributor->name }}</td>
                    <td>{{ $contributor->email }}</td>
                    <td>{{ $contributor->structure->name ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Validations History -->
    @if($saisine->validations->count() > 0)
    <div class="section">
        <div class="section-title">Historique des validations</div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Validateur</th>
                    <th>Étape</th>
                    <th>Action</th>
                    <th>Commentaire</th>
                </tr>
            </thead>
            <tbody>
                @foreach($saisine->validations as $validation)
                <tr>
                    <td>{{ $validation->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $validation->validator->name }}</td>
                    <td>{{ $validation->stage->display_name }}</td>
                    <td>
                        @if($validation->action === 'validate') Validé
                        @elseif($validation->action === 'request_completion') Complément demandé
                        @elseif($validation->action === 'partial_validation') Validation partielle
                        @elseif($validation->action === 'total_validation') Validation totale
                        @elseif($validation->action === 'reject') Rejeté
                        @endif
                    </td>
                    <td>{{ $validation->comment }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Attachments -->
    @if($saisine->attachments->count() > 0)
    <div class="section">
        <div class="section-title">Pièces jointes</div>
        <table>
            <thead>
                <tr>
                    <th>Nom du fichier</th>
                    <th>Taille</th>
                    <th>Ajouté par</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($saisine->attachments as $attachment)
                <tr>
                    <td>{{ $attachment->original_filename }}</td>
                    <td>{{ $attachment->getFormattedSize() }}</td>
                    <td>{{ $attachment->uploader->name }}</td>
                    <td>{{ $attachment->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Document généré le {{ now()->format('d/m/Y à H:i') }}</p>
        <p> OFMS </p>
    </div>
</body>
</html>
