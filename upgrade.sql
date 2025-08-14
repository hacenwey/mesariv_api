-- Table des utilisateurs
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    phone TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL
);

-- Table des clients
CREATE TABLE clients (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    phone TEXT,
    FOREIGN KEY (user_id) REFERENCES users (id)
);
ALTER TABLE clients
ADD COLUMN date TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN amount REAL NOT NULL DEFAULT 0;

-- Table des transactions (recettes et dépenses)
CREATE TABLE transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    client_id INTEGER,
    type TEXT CHECK(type IN ('recette','depense')) NOT NULL,
    amount REAL NOT NULL,
    date TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id),
    FOREIGN KEY (client_id) REFERENCES clients (id)
);

-- Table caisse centrale
CREATE TABLE caisse (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    solde REAL NOT NULL DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users (id)
);

ALTER TABLE caisse
ADD COLUMN type VARCHAR(20) NOT NULL DEFAULT 'recette'
CHECK (type IN ('recette', 'depense'));

ALTER TABLE caisse
ADD COLUMN date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE transactions
ADD COLUMN note TEXT DEFAULT NULL;

ALTER TABLE caisse
ADD COLUMN note TEXT DEFAULT NULL;