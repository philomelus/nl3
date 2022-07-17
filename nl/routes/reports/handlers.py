from flask import render_template
from flask_security import login_required

from nl.routes.reports import bp


@bp.route("/draw", methods=("GET", "POST"))
@login_required
def draw():
    return render_template("working.html", path="Routes / Reports / Draw")


@bp.route("/route", methods=("GET", "POST"))
@login_required
def route():
    return render_template("working.html", path="Routes / Reports / Route")


@bp.route("/status", methods=("GET", "POST"))
@login_required
def status():
    return render_template("working.html", path="Routes / Reports / Status")


@bp.route("/tips", methods=("GET", "POST"))
@login_required
def tips():
    return render_template("working.html", path="Routes / Reports / Tips")
