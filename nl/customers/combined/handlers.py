from flask import render_template
from flask_security import login_required

from nl import db
from nl.customers.combined import bp
from nl.customers.combined.forms import CombinedForm
from nl.models.customers import Customer, CombinedBill


@bp.route("/", methods=("GET", "POST"))
@bp.route("/index", methods=("GET", "POST"))
@login_required
def index():
    """
    Customer Combined bills form/logic.
    """
    from sqlalchemy import asc, select, distinct

    form = CombinedForm
    primary = (
        db.session.execute(select(distinct(CombinedBill.customer_id_main)))
        .scalars()
        .all()
    )
    combined = []
    for p in primary:
        info = Customer.query.filter_by(id=p).first()
        others = []

        secondaries = (
            db.session.execute(
                select(distinct(CombinedBill.customer_id_secondary)).filter(
                    CombinedBill.customer_id_main == p
                )
            )
            .scalars()
            .all()
        )
        for s in secondaries:
            oi = Customer.query.filter_by(id=s).first()
            n = oi.name()
            name = n.first
            if n.last:
                name += " " + n.last
            others.append({"id": oi.id, "name": name, "address": oi.address().address1})

        n = info.name()
        name = n.first
        if n.last:
            name += " " + n.last
        combined.append(
            {
                "id": info.id,
                "name": name,
                "address": info.address().address1,
                "count": len(others),
                "others": others,
            }
        )
    count = len(combined)

    return render_template(
        "customers/combined/index.html",
        path="Customers / Combined",
        form=form,
        count=count,
        combined=combined,
    )
