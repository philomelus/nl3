# coding: utf-8

from flask_security import RoleMixin, UserMixin

from nl import db


class Role(db.Model, RoleMixin):
    """
    Represents virtual permissions that a user needs in order to access/use
    the resource/page/object named by role.
    """
    __tablename__ = 'auth_roles'
    
    id = db.Column(db.Integer, primary_key=True)
    desc = db.Column(db.String())
    name = db.Column(db.String(80), unique=True)
    permissions = db.Column(db.UnicodeText)


class RolesUsers(db.Model):
    """
    Associates Users with Roles, providing unlimited permission posibilities.
    """
    __tablename__ = 'auth_roles_users'
    
    id = db.Column(db.Integer, primary_key=True)
    role_id = db.Column(db.ForeignKey('auth_roles.id'), nullable=False)
    user_id = db.Column(db.ForeignKey('auth_users.id'), nullable=False)

    
class User(db.Model, UserMixin):
    """
    Users of newsledger.  NOT customers (well, they could be both ...).
    """
    __tablename__ = 'auth_users'

    id = db.Column(db.Integer, primary_key=True)
    active = db.Column(db.Boolean, nullable=False)
    email = db.Column(db.String(255), nullable=False, unique=True)
    fs_uniquifier = db.Column(db.String(64), nullable=False, unique=True)
    password = db.Column(db.String(255), nullable=False)
    username = db.Column(db.String(64), nullable=False, unique=True)

    roles = db.relationship('Role', secondary='auth_roles_users', backref='users')

