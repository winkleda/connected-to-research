
LATEX	= pdflatex -shell-escape -file-line-error
#LATEX	= pdflatex -shell-escape -interaction=nonstopmode -file-line-error

all: report.pdf
midterm.pdf: winter.tex
	pygmentize -l php -f latex -o code/__parse1.tex code/parse1.php
	pygmentize -l php -f latex -o code/__parse2.tex code/parse2.php
	$(LATEX) midterm.tex
	$(LATEX) midterm.tex
final.pdf: final.tex
	pygmentize -l php -f latex -o code/__parse1.tex code/parse1.php
	pygmentize -l php -f latex -o code/__parse2.tex code/parse2.php
	$(LATEX) final.tex
	$(LATEX) final.tex
report.pdf: report.tex
	pygmentize -l php -f latex -o code/__parse1.tex code/parse1.php
	pygmentize -l php -f latex -o code/__parse2.tex code/parse2.php
	$(LATEX) report.tex
	$(LATEX) report.tex

clean:
	rm -f *.pdf *.ps *.dvi *.out *.log *.aux *.bbl *.blg *.pyg *.toc code/__*.tex

.PHONY: all show clean ps pdf showps

