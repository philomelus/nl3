from flask import render_template
from flask_security import login_required

from nl.customers.reports import bp
from nl.customers.reports.forms import *

__all__ = ["ahead", "behind", "inactive", "info"]


@bp.route("/ahead", methods=("GET", "POST"))
@login_required
def ahead():
    form = AheadForm()
    c_one = c_many = ""
    count = form.count.data or "one"
    if count == "one":
        c_one = " checked"
    else:
        c_many = " checked"
    many = form.many.data or 2
    if form.validate_on_submit():
        from nl.models.config import Config
        from nl.models.customers import (
            Customer,
            Address,
            Name,
        )
        from nl.models.routes import Sequence

        num = 1
        if count == "many":
            num = int(form.many.data)

        flagstoptype = Config.get("flag-stop-type")

        qry = (
            Customer.query.join(Sequence, Customer.id == Sequence.tag_id)
            .filter(Customer.balance < 0)
            .filter(Customer.active == "Y")
            .filter(Customer.type_id != flagstoptype)
            .order_by(Customer.route_id, Sequence.order)
        )
        records = qry.all()

        report = []
        total = 0
        totalall = 0
        count = 0
        for c in records:
            rate = c.rate()
            if c.balance <= -(num * rate):
                n = c.name()
                name = n.first
                if n.last:
                    name += " " + n.last
                report.append(
                    {
                        "id": c.id,
                        "name": name,
                        "address": c.address().address1,
                        "tid": c.type.id,
                        "type": c.type.abbr,
                        "route": c.route.title,
                        "balance": c.balance,
                        "count": abs(c.balance / rate),
                    }
                )
                total += num * rate
                totalall += c.balance
                count += 1
        title = f"Customers Ahead {num} Period"
        if num > 1:
            title += "s"
        subtitle = "{} Customers".format(count)
    else:
        report = []
        title = ""
        subtitle = ""
        total = 0
        totalall = 0

    return render_template(
        "customers/reports/ahead.html",
        path="Customers / Reports / Ahead",
        form=form,
        c_one=c_one,
        c_many=c_many,
        report=report,
        report_title=title,
        subtitle=subtitle,
        total=total,
        totalall=abs(totalall),
        many=many,
    )


@bp.route("/behind", methods=("GET", "POST"))
@login_required
def behind():
    form = BehindForm()
    what = form.what.data
    if what:
        one = many = nopmts = ""
        if what == "one":
            one = " checked"
        elif what == "many":
            many = " checked"
        elif what == "nopmts":
            nopmts = " checked"
    else:
        one = " checked"
        many = nopmts = ""

    vars = {"form": form, "one": one, "many": many, "nopmts": nopmts}
    if form.validate_on_submit():
        if what == "one":
            return behind_one(vars)
        elif what == "many":
            return behind_many(vars)
        elif what == "nopmts":
            return behind_nopmts(vars)

    return render_template(
        "customers/reports/behind.html", path="Customers / Reports / Behind", **vars
    )


def behind_one(vars):
    from nl.models.customers import Customer
    from nl.models.routes import Sequence

    from flask import current_app

    current_app.logger.debug("behind_one called")

    qry = (
        Customer.query.join(Sequence, Customer.id == Sequence.tag_id)
        .filter(Customer.balance < 0)
        .filter(Customer.active == "Y")
        .order_by(Customer.route_id, Sequence.order)
    )
    records = qry.all()

    report = []
    count = 0
    for c in records:
        rate = c.rate()
        diff = c.balance - (2 * rate)
        if diff >= 0 and diff <= rate:
            n = c.name()
            name = n.first
            if n.last:
                name += " " + n.last
            report.append(
                {
                    "id": c.id,
                    "name": name,
                    "address": c.address().address1,
                    "tid": c.type.id,
                    "type": c.type.abbr,
                    "route": c.route.title,
                    "balance": c.balance,
                }
            )
            count += 1
    title = f"Customers Behind 1 Period"
    subtitle = "{} Customer".format(count)
    if count == 0 or count > 1:
        subtitle += "s"

    return render_template(
        "customers/reports/behind.html",
        path="Customers / Reports / Behind",
        **vars,
        report_title=title,
        subtitle=subtitle,
        report=report,
        count=count,
        doReport=True,
    )


def behind_many(vars):
    from nl.models.customers import Customer
    from nl.models.routes import Sequence

    from flask import current_app

    current_app.logger.debug("behind_one called")

    qry = (
        Customer.query.join(Sequence, Customer.id == Sequence.tag_id)
        .filter(Customer.balance < 0)
        .filter(Customer.active == "Y")
        .order_by(Customer.route_id, Sequence.order)
    )
    records = qry.all()

    report = []
    count = 0
    for c in records:
        rate = c.rate()
        diff = c.balance - (3 * rate)
        if diff >= 0:
            n = c.name()
            name = n.first
            if n.last:
                name += " " + n.last
            report.append(
                {
                    "id": c.id,
                    "name": name,
                    "address": c.address().address1,
                    "tid": c.type.id,
                    "type": c.type.abbr,
                    "route": c.route.title,
                    "balance": c.balance,
                }
            )
            count += 1
    title = f"Customers Behind More Than 1 Period"
    subtitle = "{} Customer".format(count)
    if count == 0 or count > 1:
        subtitle += "s"

    return render_template(
        "customers/reports/behind.html",
        path="Customers / Reports / Behind",
        **vars,
        report_title=title,
        subtitle=subtitle,
        report=report,
        count=count,
        doReport=True,
    )


def behind_nopmts(vars):
    from nl.models.customers import Customer
    from nl.models.routes import Sequence

    from flask import current_app

    current_app.logger.debug("behind_one called")

    sq = Customer.query.join(Customer.payments)
    qry = (
        Customer.query.join(Sequence, Customer.id == Sequence.tag_id)
        .filter(Customer.balance < 0)
        .filter(Customer.active == "Y")
        .order_by(Customer.route_id, Sequence.order)
    )
    records = qry.all()

    report = []
    count = 0
    for c in records:
        rate = c.rate()
        diff = c.balance - (3 * rate)
        if diff >= 0 and len(c.payments) == 0:
            n = c.name()
            name = n.first
            if n.last:
                name += " " + n.last
            report.append(
                {
                    "id": c.id,
                    "name": name,
                    "address": c.address().address1,
                    "tid": c.type.id,
                    "type": c.type.abbr,
                    "route": c.route.title,
                    "balance": c.balance,
                }
            )
            count += 1
    title = f"Customers Behind  1 Period With No Payments"
    subtitle = "{} Customer".format(count)
    if count == 0 or count > 1:
        subtitle += "s"

    return render_template(
        "customers/reports/behind.html",
        path="Customers / Reports / Behind",
        **vars,
        report_title=title,
        subtitle=subtitle,
        report=report,
        count=count,
        doReport=True,
    )


@bp.route("/inactive", methods=("GET", "POST"))
@login_required
def inactive():
    form = InactiveForm()
    doReport = False

    if form.validate_on_submit():
        from nl.models.customers import Customer
        from nl.models.routes import Sequence

        routeList = form.routeList.data

        qry = Customer.query.join(Sequence, Customer.id == Sequence.tag_id).filter(
            Customer.active == "N"
        )
        if routeList == "1":
            qry = qry.filter(Customer.routeList == "Y")
        elif routeList == "2":
            qry = qry.filter(Customer.routeList == "N")
        records = qry.order_by(Customer.route_id, Sequence.order).all()

        report = []
        count = 0
        for c in records:
            n = c.name()
            name = n.first
            if n.last:
                name += " " + n.last
            report.append(
                {
                    "id": c.id,
                    "name": name,
                    "address": c.address().address1,
                    "tid": c.type.id,
                    "type": c.type.abbr,
                    "route": c.route.title,
                }
            )
            count += 1
        subtitle = "{} Customer".format(count)
        if count == 0 or count > 1:
            subtitle += "s"
        doReport = True
    else:
        routeList = "1"
        report = []
        subtitle = ""
        count = 0

    return render_template(
        "customers/reports/inactive.html",
        path="Customers / Reports / Inactive",
        form=form,
        routeList=routeList,
        subtitle=subtitle,
        report=report,
        doReport=doReport,
        count=count,
    )


@bp.route("/info", methods=("GET", "POST"))
@login_required
def info():
    form = InfoForm()
    doReport = False

    if form.validate_on_submit():
        from nl.models.customers import (
            Customer,
            Address,
            Adjustment,
            Name,
            Payment,
            Telephone,
        )
        from nl.models.routes import Sequence

        def nam(rec):
            n = rec.first
            if rec.last:
                n += " " + rec.last
            return n

        qry = (
            Customer.query.join(Sequence, Customer.id == Sequence.tag_id)
            .filter(Customer.active == "Y", Customer.routeList == "Y")
            .order_by(Customer.route_id, Sequence.order)
        )
        records = qry.all()
        report = []
        for c in records:
            names = (
                Name.query.filter(Name.customer_id == c.id)
                .order_by(Name.sequence)
                .all()
            )
            addresses = (
                Address.query.filter(Address.customer_id == c.id)
                .order_by(Address.sequence)
                .all()
            )
            telephones = (
                Telephone.query.filter(Telephone.customer_id == c.id)
                .order_by(Telephone.sequence)
                .all()
            )
            record = {}
            record["id"] = c.id

            # Delivery names
            record["name"] = nam(names[0])
            if len(names) > 1:
                for n in names:
                    if n.sequence == Name.NAM_DELIVERY2:
                        record["name2"] = nam(n)
                        break

            # Delivery address
            record["address1"] = addresses[0].address1
            record["address2"] = addresses[0].address2
            record["city"] = addresses[0].city
            record["state"] = addresses[0].state
            record["postal"] = addresses[0].zip

            # Delivery telephones
            record["telephone1"] = dict(
                type=telephones[0].type, number=telephones[0].number
            )
            if len(telephones) > 1:
                for t in telephones:
                    if t.sequence == Telephone.TEL_DELIVERY2:
                        record["telephone2"] = dict(type=t.type, number=t.number)
                    elif t.sequence == Telephone.TEL_DELIVERY3:
                        record["telephone3"] = dict(type=t.type, number=t.number)

            # Delivery info
            record["route"] = c.route_id
            record["type"] = c.type.name
            record["type_abbr"] = c.type.abbr
            record["balance"] = c.balance
            record["notes"] = c.notes
            record["delivery_note"] = c.deliveryNote
            record["bill_note"] = c.billNote

            # Rate info
            record["rate_type"] = c.rateType
            record["rate_extra"] = c.rateOverride
            record["rate_final"] = c.rate()

            # Billing?
            if len(addresses) > 1:
                if (
                    addresses[0].address1 != addresses[1].address1
                    or addresses[0].address2 != addresses[1].address2
                ):
                    billing = {}
                    for n in names:
                        if n.sequence == Name.NAM_BILLING1:
                            billing["name"] = nam(n)
                        elif n.sequence == Name.NAM_BILLING2:
                            billing["name2"] = nam(n)
                    billing["address1"] = addresses[1].address1
                    billing["address2"] = addresses[1].address2
                    billing["city"] = addresses[1].city
                    billing["state"] = addresses[1].state
                    billing["zip"] = addresses[1].zip
                    for t in telephones:
                        if t.sequence == Telephone.TEL_BILLING1:
                            billing["telephone1"] = dict(type=t.type, number=t.number)
                        elif t.sequence == Telephone.TEL_BILLING2:
                            billing["telephone2"] = dict(type=t.type, number=t.number)
                        elif t.sequence == Telephone.TEL_BILLING3:
                            billing["telephone3"] = dict(type=t.type, number=t.number)
                    record["billing"] = billing
                else:
                    record["billing"] = None
            else:
                record["billing"] = None

            # Adjustments?
            if form.adjustments.data:
                adjrecs = (
                    Adjustment.query.filter(Adjustment.customer_id == c.id)
                    .order_by(
                        Adjustment.period_id, Adjustment.created, Adjustment.updated
                    )
                    .all()
                )
                adjustments = []
                for adj in adjrecs:
                    adjustment = {}
                    adjustment["period"] = adj.period.title
                    adjustment["amount"] = adj.amount
                    adjustment["desc"] = adj.desc
                    adjustments.append(adjustment)
                record["adjustments"] = adjustments

            # Payments
            if form.payments.data:
                pmtrecs = (
                    Payment.query.filter(Payment.customer_id == c.id)
                    .order_by(
                        Payment.period_id,
                        Payment.date,
                        Payment.created,
                        Payment.updated,
                    )
                    .all()
                )
                payments = []
                for p in pmtrecs:
                    payment = {}
                    payment["period"] = p.period.title
                    payment["amount"] = p.amount
                    payment["tip"] = p.tip
                    payment["type"] = p.type
                    payment["extra1"] = p.extra1
                    payments.append(payment)
                record["payments"] = payments

            report.append(record)
        count = len(report)
        doReport = True
    else:
        count = 0
        report = []

    return render_template(
        "customers/reports/info.html",
        path="Customer / Reports / Info",
        form=form,
        doReport=doReport,
        count=count,
        report=report,
    )
