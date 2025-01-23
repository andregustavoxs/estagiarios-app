<?php

return [
    'required' => 'O campo :attribute é obrigatório.',
    'unique' => 'Este :attribute já está em uso.',
    'email' => 'O campo :attribute deve ser um endereço de e-mail válido.',
    'max' => [
        'string' => 'O campo :attribute não pode ter mais que :max caracteres.',
    ],
    'attributes' => [
        // Intern fields
        'name' => 'nome',
        'email' => 'e-mail',
        'phone' => 'telefone',
        'birth_date' => 'data de nascimento',
        'cpf' => 'CPF',
        'rg' => 'RG',
        'address' => 'endereço',
        'city' => 'cidade',
        'state' => 'estado',
        'zip_code' => 'CEP',

        // Internship fields
        'intern_id' => 'estagiário',
        'registration_number' => 'matrícula',
        'start_date' => 'data de início',
        'end_date' => 'data de término',
        'course_id' => 'curso',
        'department_id' => 'setor',
        'supervisor_id' => 'supervisor',
        'internship_agency_id' => 'agente de integração',

        // Course fields
        'code' => 'código',
        'vacancies' => 'vagas',
        'coordinator' => 'coordenador',

        // Department fields
        'acronym' => 'sigla',
        'manager' => 'gestor',
        'description' => 'descrição',

        // Internship Agency fields
        'trade_name' => 'nome fantasia',
        'company_name' => 'razão social',
        'cnpj' => 'CNPJ',
        'contact_name' => 'nome do contato',
        'contact_email' => 'e-mail do contato',
        'contact_phone' => 'telefone do contato',

        // Supervisor fields
        'registration' => 'matrícula',
        'position' => 'cargo',
        'department' => 'setor',
    ],
];
