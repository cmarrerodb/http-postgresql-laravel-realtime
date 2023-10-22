CREATE TRIGGER send_to_api_trigger
    AFTER INSERT
    ON public.tabla_base_a --AQÍ SE COLOCA EL NOMBRE DE LA TABLA A MONITOREAR
    FOR EACH ROW
    EXECUTE FUNCTION public.send_to_api();