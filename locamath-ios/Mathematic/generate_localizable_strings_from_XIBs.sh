find . -name "*.xib" | while IFS= read -r pathname; do
    base=$(basename "$pathname"); name=${base%.*};
    echo "$pathname"
    ibtool --generate-strings-file "en.lproj/${name}.strings" "$pathname"
done