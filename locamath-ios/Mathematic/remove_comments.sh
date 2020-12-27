find . -name "*.strings" | while IFS= read -r pathname; do
    base=$(basename "$pathname"); name=${base%.*};
    echo "$pathname";
    iconv -f utf-16 -t utf-8 "$pathname" | sed -e 's/\/\*[^\*\/]*\*\///g' | iconv -f utf-8 -t utf-16 | cat > "${name}.strings";
done