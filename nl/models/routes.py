# coding: utf-8

from nl import db


__all__ = [
    'Route',
    'ChangeNote',
    'Sequence',
]

class Route(db.Model):
    """
    Details of a specific route.  Each customer can only be on a single route.
    """
    __tablename__ = 'routes'

    id = db.Column(db.Integer, primary_key=True)
    created = db.Column(db.DateTime, nullable=False)
    updated = db.Column(db.DateTime, nullable=False)
    title = db.Column(db.String(20), nullable=False)
    active = db.Column(db.Enum('N', 'Y'), nullable=False)

    #customers = db.relationship('Customer', primaryjoin='Route.id == customers.c.route_id')
    sequences = db.relationship('Sequence', primaryjoin='Route.id == routes_sequence.c.route_id',
                                backref='route')
    

class ChangeNote(db.Model):
    """
    For daily change list (for drivers), a global note (when route_id = NULL),
    as well as a route specific note.  Allows communication to drivers w/o
    talking face to face (or used as a reminder).
    """
    __tablename__ = 'routes_changes_notes'

    id = db.Column(db.Integer, primary_key=True)
    date = db.Column(db.Date, nullable=False, index=True)
    route_id = db.Column(db.ForeignKey('routes.id', ondelete='RESTRICT', onupdate='CASCADE'),
                         nullable=True, index=True)
    created = db.Column(db.DateTime, nullable=False)
    updated = db.Column(db.DateTime, nullable=False)
    note = db.Column(db.String(), nullable=False)

    route = db.relationship('Route', primaryjoin='ChangeNote.route_id == routes.c.id',
                            backref='change_notes')


class Sequence(db.Model):
    """
    For each customer on each route, the order of the customer in relation to other customers
    on the same route.
    """
    __tablename__ = 'routes_sequence'

    tag_id = db.Column(db.ForeignKey('customers.id', ondelete='RESTRICT', onupdate='CASCADE'),
                       primary_key=True)
    route_id = db.Column(db.ForeignKey('routes.id', ondelete='CASCADE', onupdate='CASCADE'),
                         primary_key=True)
    order = db.Column(db.Integer, nullable=False)

#    route = db.relationship('Route', primaryjoin='Sequence.route_id == routes.c.id',
#                            backref='sequences')
    customer = db.relationship('Customer', primaryjoin='Sequence.tag_id == customers.c.id',
                               backref='sequence')
    
