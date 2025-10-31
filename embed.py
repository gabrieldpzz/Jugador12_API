#!/usr/bin/env python3
"""
Embed productos con Ollama (nomic-embed-text) y guarda en pgvector (vector(768)).
Requisitos:
  pip install requests psycopg2-binary python-dotenv
"""

import os, time, math, json, requests, psycopg2
from psycopg2.extras import execute_values
from dotenv import load_dotenv
from psycopg2.extras import execute_batch

load_dotenv()  # opcional .env

DB_HOST = os.getenv("DB_HOST", "localhost")
DB_PORT = int(os.getenv("DB_PORT", "5432"))
DB_NAME = os.getenv("DB_DATABASE", "jugador12")
DB_USER = os.getenv("DB_USERNAME", "postgres")
DB_PASS = os.getenv("DB_PASSWORD", "postgres")

OLLAMA_EMBED_URL   = os.getenv("OLLAMA_EMBED_URL", "http://192.168.1.5:11434/api/embeddings")
OLLAMA_EMBED_MODEL = os.getenv("OLLAMA_EMBED_MODEL", "nomic-embed-text")

BATCH_SIZE   = int(os.getenv("EMBED_BATCH", "16"))
SLEEP_BETWEEN = float(os.getenv("EMBED_SLEEP", "0.1"))

def l2_normalize(vec):
    norm = math.sqrt(sum(x*x for x in vec))
    return vec if norm == 0 else [x/norm for x in vec]

def build_text(row):
    parts = [
        f"Nombre: {row['name']}",
        f"Equipo: {row.get('team') or ''}",
        f"Categoría: {row.get('category') or ''}",
        f"Descripción: {row.get('description') or ''}",
    ]
    return "\n".join([p for p in parts if p.strip()])

def fetch_pending(conn, limit):
    with conn.cursor() as cur:
        cur.execute("""
            SELECT id, name, team, category, description
            FROM products
            WHERE embedding IS NULL
            ORDER BY id
            LIMIT %s
        """, (limit,))
        cols = [d.name for d in cur.description]
        return [dict(zip(cols, r)) for r in cur.fetchall()]

def emb_text(text):
    r = requests.post(OLLAMA_EMBED_URL, json={"model": OLLAMA_EMBED_MODEL, "prompt": text}, timeout=60)
    r.raise_for_status()
    data = r.json()
    if "embedding" in data:
        vec = data["embedding"]
    elif "embeddings" in data and data["embeddings"]:
        vec = data["embeddings"][0]
    else:
        raise RuntimeError(f"Respuesta inesperada de Ollama: {data}")
    if not isinstance(vec, list) or not vec:
        raise RuntimeError("Embedding vacío")
    return vec

def update_embeddings(conn, items):
    """
    items: list[tuple[int, list[float]]] -> [(id, vec), ...]
    """
    with conn.cursor() as cur:
        # Prepara el vector como string literal: "[v1, v2, ...]"
        data = [(f"[{', '.join(str(x) for x in vec)}]", pid) for (pid, vec) in items]
        execute_batch(
            cur,
            "UPDATE products SET embedding = %s::vector WHERE id = %s",
            data,
            page_size=100
        )
    conn.commit()

def main():
    print(f"→ Conectando a Postgres {DB_USER}@{DB_HOST}:{DB_PORT}/{DB_NAME}")
    conn = psycopg2.connect(host=DB_HOST, port=DB_PORT, dbname=DB_NAME, user=DB_USER, password=DB_PASS)

    total = 0
    while True:
        rows = fetch_pending(conn, BATCH_SIZE)
        if not rows:
            print(f"✔ Sin pendientes. Total embebidos: {total}")
            break

        updates = []
        for r in rows:
            txt = build_text(r)
            try:
                vec = emb_text(txt)
                vec = l2_normalize(vec)
                updates.append((r["id"], vec))
                print(f"  ✓ id={r['id']} dim={len(vec)}")
            except Exception as e:
                print(f"  × id={r['id']} error: {e}")
            time.sleep(SLEEP_BETWEEN)

        if updates:
            update_embeddings(conn, updates)
            total += len(updates)

    conn.close()

if __name__ == "__main__":
    main()
