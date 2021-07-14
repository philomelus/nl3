
import functools

from flask import (Blueprint, flash, g, redirect, render_template,
                   request, session, url_for)
from werkzeug.security import check_password_hash, generate_password_hash

from nl3.db import get_db


bp = Blueprint('auth', __name__, url_prefix='/auth')


@bp.before_app_request
def load_logged_in_user():
    user_id = session.get('user_id')

    if user_id is None:
        g.user = None
    else:
        g.user = get_db().execute('SELECT * FROM user WHERE id = ?', (user_id,)
                                  ).fetchone()


@bp.route('/login', methods=('GET', 'POST'))
def login():
    if request.method == 'POST':
        username = request.form['username']
        password = request.form['password']
        db = get_db()
        error = None

        user = db.execute('SELECT * FROM user WHERE username = ?', (username,)
                          ).fetchone()

        if user is None:
            error = 'Incorrect username.'
        elif not check_password_hash(user['password'], password):
            error = 'Incorrect password.'

        if error is None:
            session.clear()
            session['user_id'] = user['id']
            return redirect(url_for('index'))

        flash(error)

    return render_template('auth/login.html')


def login_required(view):
    """Decorator to force login before accessing a enpoint."""
    @functools.wraps(view)
    def wrapped_view(**kwargs):
        if g.user is None:
            return redirect(url_for('auth.login'))

        return view(**kwargs)

    return wrapped_view


@bp.route('/logout')
def logout():
    """Log out the currently logged in  user."""
    session.clear()
    return redirect(url_for('index'))


@bp.route('/register', methods=('GET', 'POST'))
def register():
    """Register a new user."""
    if request.method == 'POST':
        username = request.form['username']
        password = request.form['password']
        db = get_db()
        error = None

        if not username or not password:
            error = 'Username and Password are required.'
        elif db.session.execute('SELECT id FROM user WHERE username = ?', (username,)
                        ).fetchone() is not None:
            error = f"User {username} is already registered."

        if error is None:
            from .models import User
            db.session.execute('INSERT INTO user (username, password) VALUES (?, ?)',
                       (username, generate_password_hash(password)))
            db.session.commit()
            return redirect(url_for('auth.login'))

        flash(error)

    return render_template('auth/register.html')

