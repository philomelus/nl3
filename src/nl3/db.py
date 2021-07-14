
from flask_sqlalchemy import SQLAlchemy

import click

from flask import current_app, g
from flask.cli import with_appcontext


def close_db(e=None):
    db = g.pop('db', None)
    if db is not None:
        db.close()

        
def get_db():
    if 'db' not in g:
        g.db = SQLAlchemy()
    return g.db


def init_app(app):
    #app.teardown_appcontext(close_db)
    app.cli.add_command(init_db_command)
    

def init_db():
    pass


@click.command('init-db')
@with_appcontext
def init_db_command():
    init_db()
    click.echo('Database initialized.')


