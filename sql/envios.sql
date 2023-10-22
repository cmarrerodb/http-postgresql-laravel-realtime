CREATE TABLE IF NOT EXISTS public.envios
(
    id serial NOT NULL,
    payload jsonb NOT NULL,
    status boolean,
    created_at timestamp without time zone DEFAULT now(),
    updated_at timestamp with time zone,
    CONSTRAINT envios_pkey PRIMARY KEY (id)
);