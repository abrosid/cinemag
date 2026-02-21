#!/bin/bash
# Download movie posters for Cinemag

POSTERS_DIR="/Users/devel/cinemag/www/images/posters"
mkdir -p "$POSTERS_DIR"

# Array of movies with their TMDB/IMDb poster URLs
# Using TMDB poster URLs (small size ~185px width)
movies=(
    "matrix-poster.jpg|https://m.media-amazon.com/images/M/MV5BNzQzOTk3OTAtNDQ0Zi00ZTVkLWI0MTEtMDllZjNkYzNjNTc4L2ltYWdlXkEyXkFqcGdeQXVyNjU0OTQ0OTY@._V1_.jpg"
    "inception-poster.jpg|https://m.media-amazon.com/images/M/MV5BMjAxNDA3ODMyNV5BMl5BanBnXkFtZTcwMzI2OTExMw@@._V1_.jpg"
    "interstellar-poster.jpg|https://m.media-amazon.com/images/M/MV5BZjdkOTU3MDktN2IxOS00OGEyLWFmMjktY2FiMmZkNWIyODZiXkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_.jpg"
    "dark-knight-poster.jpg|https://m.media-amazon.com/images/M/MV5BMTMxNTMwODM0NF5BMl5BanBnXkFtZTcwODAyMTk2Mw@@._V1_.jpg"
    "pulp-fiction-poster.jpg|https://m.media-amazon.com/images/M/MV5BNGNhMDIzZTUtNTBlZi00MTRlLWFjM2ItYzViMjE3YzI5MjljXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg"
    "shawshank-poster.jpg|https://m.media-amazon.com/images/M/MV5BNDE3ODcxYzMtY2YzZC00NmNlLWJiNDMtZDViZWM2MzIxZDYwXkEyXkFqcGdeQXVyNjAwNDUxODI@._V1_.jpg"
    "forrest-gump-poster.jpg|https://m.media-amazon.com/images/M/MV5BNWIwODRlZTUtY2U3ZS00Yzg1LWJhNzYtMmZiYmEyNmU1NjMzXkEyXkFqcGdeQXVyMTQxNzMzNDI@._V1_.jpg"
    "godfather-poster.jpg|https://m.media-amazon.com/images/M/MV5BM2MyNjYxNmUtYTAwNi00MTYxLWJmNWYtYzZlODY3ZTk3OTFlXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg"
    "fight-club-poster.jpg|https://m.media-amazon.com/images/M/MV5BOTgyOGQ1NDItNGU3Ny00MjU3LTg2YWEtNmEyYjBiMjI1Y2M5XkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg"
    "goodfellas-poster.jpg|https://m.media-amazon.com/images/M/MV5BM2MyNjYxNmUtYTAwNi00MTYxLWJmNWYtYzZlODY3ZTk3OTFlXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg"
    "departed-poster.jpg|https://m.media-amazon.com/images/M/MV5BMTI1OTYxNzAxOF5BMl5BanBnXkFtZTYwNTE5ODI4._V1_.jpg"
    "prestige-poster.jpg|https://m.media-amazon.com/images/M/MV5BMjM2NzgxNjktY2I0NS00YjQ5LTkxODktOGY2MmZiYzQwNzE5XkEyXkFqcGdeQXVyNTIzOTk5MTM@._V1_.jpg"
    "gladiator-poster.jpg|https://m.media-amazon.com/images/M/MV5BM2UwZDZkZDgtMGQ2OC00NjAxLWEzYTQtYTI1NGVmZmFlNjdiL2ltYWdlXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg"
    "lotr-fellowship-poster.jpg|https://m.media-amazon.com/images/M/MV5BN2EyZjM3NzUtNWUzMi00MTgxLWI0NTctMzY4NjI5OTg1OTUyXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg"
    "lotr-return-poster.jpg|https://m.media-amazon.com/images/M/MV5BNzkwODFjNzItMmMwNi00MTU5LWE2MzktM2M4ZDczZGM1MmViXkEyXkFqcGdeQXVyNDc4NjYxNTY@._V1_.jpg"
    "django-poster.jpg|https://m.media-amazon.com/images/M/MV5BMTUxMzk1MTQyMl5BMl5BanBnXkFtZTcwNzA2NDEzMQ@@._V1_.jpg"
    "inglourious-basterds-poster.jpg|https://m.media-amazon.com/images/M/MV5BOTJiOWE2NjQtOThlMi00MTA3LThkYzQtNzJkYjBkMGE0YzE1XkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg"
    "revenant-poster.jpg|https://m.media-amazon.com/images/M/MV5BMTc4NzA5MTIwNV5BMl5BanBnXkFtZTcwNjIxNjk3Nw@@._V1_.jpg"
    "mad-max-poster.jpg|https://m.media-amazon.com/images/M/MV5BN2EwNGE4OTQtZmMwOS00MzQ5LTg3YzUtZTEwZjZiYWRlNGFiXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg"
    "blade-runner-poster.jpg|https://m.media-amazon.com/images/M/MV5BNzQzMzJhZTEtOWM4NS00MTdhLTg0YjgtMjM4MDRkZjUwZDBlXkEyXkFqcGdeQXVyNjU0OTQ0OTY@._V1_.jpg"
)

echo "Downloading movie posters..."
for movie in "${movies[@]}"; do
    filename="${movie%%|*}"
    url="${movie##*|}"
    echo -n "Downloading $filename... "
    curl -sL -o "$POSTERS_DIR/$filename" "$url" 2>/dev/null
    if [ -s "$POSTERS_DIR/$filename" ]; then
        echo "OK ($(wc -c < "$POSTERS_DIR/$filename") bytes)"
    else
        echo "FAILED"
    fi
done

echo ""
echo "Done! Posters saved to $POSTERS_DIR"
ls -la "$POSTERS_DIR"
