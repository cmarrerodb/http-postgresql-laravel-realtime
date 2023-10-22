CREATE OR REPLACE FUNCTION public.send_to_api()
    RETURNS trigger
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE NOT LEAKPROOF
AS $BODY$
DECLARE
    req http_request;
    res http_response;
    nuevo jsonb;
    url text;
    tabla text;
    registro record;
BEGIN
    tabla := TG_TABLE_NAME; 
    nuevo := row_to_json(NEW);
    nuevo := jsonb_set(nuevo, '{tabla}', to_jsonb(tabla), true); 
    url := 'http://127.0.0.1:8000/api/post';
    FOR registro IN SELECT * FROM envios WHERE envios.status = false LOOP
    BEGIN
        req := http_post(url, registro.payload);
        IF req.method::integer = 200 THEN
        UPDATE envios SET status = true, updated_at = NOW() WHERE id = registro.id;
        ELSE
        CONTINUE;
        END IF;
    EXCEPTION
        WHEN others THEN
        CONTINUE;
    END;
    END LOOP;
    BEGIN
        req := http_post(url, nuevo);
        RAISE NOTICE '%', FORMAT('%L', to_json(req));
        IF req.method::integer = 200 THEN
            RETURN NEW;
        ELSE
            INSERT INTO envios (payload, status) VALUES (nuevo, false);
            RETURN NULL;
        END IF;
        EXCEPTION
        WHEN others THEN
            INSERT INTO envios (payload, status) VALUES (nuevo, false);
            RETURN NULL;
    END;
END;
$BODY$;
