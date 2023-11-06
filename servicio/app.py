import os
import threading
import PySimpleGUI as sg
import json
from flask import Flask, request, jsonify
from werkzeug.serving import make_server

app = Flask(__name__)
current_request = ''

@app.route('/', methods=['POST'])
def home():
    data = request.get_json()
    formatted_data = json.dumps(data, indent=4)  # Formatear el JSON
    window['-CURRENT-'].update(f'Solicitud recibida:\n{formatted_data}')
    window['-HISTORY-'].print(f'Solicitud recibida:\n{formatted_data}')
    status_code = int(window['-STATUS-'].get())
    return jsonify({"status": status_code})

@app.route('/shutdown', methods=['POST'])
def shutdown():
    shutdown_server = request.environ.get('werkzeug.server.shutdown')
    if shutdown_server:
        shutdown_server()
    else:
        raise RuntimeError("¡Servidor Werkzeug no se encontró!")
    return 'Server shutting down...'

class Server(threading.Thread):
    def __init__(self, port):
        threading.Thread.__init__(self)
        self.srv = make_server('localhost', port, app)
        self.ctx = app.app_context()
        self.ctx.push()

    def run(self):
        self.srv.serve_forever()

    def shutdown(self):
        self.srv.shutdown()

sg.set_options(font=("Helvetica", 16), text_color='white', background_color='black')

layout = [
    [sg.Text('Puerto:'), sg.Input(default_text='5000', key='-PORT-', size=(10,1)), sg.Text('Status:'), sg.Input(default_text='200', key='-STATUS-', size=(10,1)),sg.Checkbox('Mantener en la parte superior', default=True, key='-TOP-'), sg.Button('Iniciar'), sg.Button('Detener'), sg.Button('Limpiar'), sg.Button('Cerrar') ],
    [sg.Text('Solicitud:'), sg.Output(size=(80,5), key='-CURRENT-')],
    [sg.Text('Historial:'), sg.Output(size=(80,20), key='-HISTORY-')],
]

window = sg.Window('CRMM - Servicio de Escucha', layout, resizable=True)
server = None

while True:
    event, values = window.read()
    if event == sg.WINDOW_CLOSED or event == 'Cerrar':
        if server:
            server.shutdown()
        break
    if event == 'Iniciar':
        port = int(values['-PORT-'])
        server = Server(port)
        server.start()
        window.TKroot.wm_attributes('-topmost', values['-TOP-'])
    elif event == 'Detener':
        if server:
            server.shutdown()
            server = None
    elif event == 'Limpiar':
        window['-CURRENT-'].update('')
        window['-HISTORY-'].update('')

window.close()
