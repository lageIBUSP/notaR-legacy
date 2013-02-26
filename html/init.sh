su - rserve -c "R --vanilla <<EOF
library(Rserve)
Rserve(args=\"--no-save\")
EOF"
