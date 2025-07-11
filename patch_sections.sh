while read file; do
  if ! grep -q "uniqid()" "$file"; then
    sed -i "1a \$id = \$id ?? basename(__FILE__, '.php') . '-' . uniqid();" "$file"
  fi
  if grep -q '<section' "$file"; then
    if ! grep -q 'id="<?php' "$file"; then
      sed -i '0,/<section/{s/<section/<section id="<?php echo htmlspecialchars($id); ?>"/}' "$file"
    fi
  elif grep -q '<nav' "$file"; then
    if ! grep -q 'id="<?php' "$file"; then
      sed -i '0,/<nav/{s/<nav/<nav id="<?php echo htmlspecialchars($id); ?>"/}' "$file"
    fi
  elif grep -q '<header' "$file"; then
    if ! grep -q 'id="<?php' "$file"; then
      sed -i '0,/<header/{s/<header/<header id="<?php echo htmlspecialchars($id); ?>"/}' "$file"
    fi
  elif grep -q '<footer' "$file"; then
    if ! grep -q 'id="<?php' "$file"; then
      sed -i '0,/<footer/{s/<footer/<footer id="<?php echo htmlspecialchars($id); ?>"/}' "$file"
    fi
  elif grep -q '<div' "$file"; then
    if ! grep -q 'id="<?php' "$file"; then
      sed -i '0,/<div/{s/<div/<div id="<?php echo htmlspecialchars($id); ?>"/}' "$file"
    fi
  elif grep -q '<hr' "$file"; then
    if ! grep -q 'id="<?php' "$file"; then
      sed -i '0,/<hr/{s/<hr/<hr id="<?php echo htmlspecialchars($id); ?>"/}' "$file"
    fi
  fi
done < section_files.txt
