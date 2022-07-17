from datetime import datetime, timezone

from flask import redirect, request, url_for
from flask_security import login_required

from nl import db
from nl.customers.combined.popups import bp
from nl.models.customers import CombinedBill
from nl.utils import flash_success


@bp.route("/add", methods=("POST",))
@login_required
def add():
    """
    Add new customer to existing combined customer.
    """
    c = CombinedBill()
    c.customer_id_main = int(request.form["add-id1"])
    c.customer_id_secondary = int(request.form["add-id2"])
    c.created = c.updated = datetime.now(timezone.utc)
    db.session.add(c)
    db.session.commit()
    flash_success("Combined Customer Added.")
    return redirect(url_for("customers.combined.index"))


@bp.route("/create", methods=("POST",))
@login_required
def create():
    """
    Create new combined customers.
    """
    c = CombinedBill()
    c.customer_id_main = int(request.form["create-id1"])
    c.customer_id_secondary = int(request.form["create-id2"])
    c.created = c.updated = datetime.now(timezone.utc)
    db.session.add(c)
    db.session.commit()
    flash_success("Combined Customer Created.")
    return redirect(url_for("customers.combined.index"))


@bp.route("/delete", methods=("POST",))
@login_required
def delete():
    """
    Delete combined customer.
    """
    id1 = int(request.form["delete-id1"])
    id2 = int(request.form["delete-id2"])
    c = CombinedBill.query.filter_by(
        customer_id_main=id1, customer_id_secondary=id2
    ).first()
    db.session.delete(c)
    db.session.commit()
    flash_success("Combined Bill Deleted.")
    return redirect(url_for("customers.combined.index"))


@bp.route("/update", methods=("POST",))
@login_required
def update():
    """
    Change combined customer id.
    """
    id1 = int(request.form["update-id1"])
    idold = int(request.form["update-id2-original"])
    idnew = int(request.form["update-id2"])
    c = CombinedBill.query.filter_by(
        customer_id_main=id1, customer_id_secondary=idold
    ).first()
    c.customer_id_secondary = idnew
    db.session.commit()
    flash_success("Combined Bill Updated.")
    return redirect(url_for("customers.combined.index"))
