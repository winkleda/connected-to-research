
LATEX	= pdflatex -shell-escape -file-line-error
#LATEX	= pdflatex -shell-escape -interaction=nonstopmode -file-line-error

all: midterm.pdf
midterm.pdf: midterm.tex
	pygmentize -l php -f latex -o code/__parse1.tex code/parse1.php
	pygmentize -l php -f latex -o code/__parse2.tex code/parse2.php
	$(LATEX) midterm.tex
	$(LATEX) midterm.tex

clean:
	rm -f *.pdf *.ps *.dvi *.out *.log *.aux *.bbl *.blg *.pyg *.toc code/__*.tex

.PHONY: all show clean ps pdf showps

