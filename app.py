from flask import Flask

app = Flask(__name__)

@app.route('/')
def hello():
    return {
        "message": "Hola desde Docker!",
        "system": "Desarrollado en Windows, listo para Ubuntu",
        "status": "Astro-Perfecto"
    }

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=8000)
