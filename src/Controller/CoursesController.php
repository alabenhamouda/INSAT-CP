<?php


namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CoursesController
 * @package App\Controller
 * @Route("/courses")
 */
class CoursesController extends AbstractController
{
    /**
     * @Route("/",name="courses")
     */
    public function courses()
    {
        return $this->render('courses/courses.html.twig');

    }
    /**
     * @Route("/sorting-and-search", name="sorting")
     */
    public function sorting()
    {
        $x=<<<EOF
// C++ program for implementation of Bubble sort
#include <bits/stdc++.h>
using namespace std;

void swap(int *xp, int *yp)
{
	int temp = *xp;
	*xp = *yp;
	*yp = temp;
}

// A function to implement bubble sort
void bubbleSort(int arr[], int n)
{
	int i, j;
	for (i = 0; i < n-1; i++)	
	
	// Last i elements are already in place
	for (j = 0; j < n-i-1; j++)
		if (arr[j] > arr[j+1])
			swap(&arr[j], &arr[j+1]);
}

/* Function to print an array */
void printArray(int arr[], int size)
{
	int i;
	for (i = 0; i < size; i++)
		cout << arr[i] << " ";
	cout << endl;
}

// Driver code
int main()
{
	int arr[] = {64, 34, 25, 12, 22, 11, 90};
	int n = sizeof(arr)/sizeof(arr[0]);
	bubbleSort(arr, n);
	cout<<"Sorted array: \n";
	printArray(arr, n);
	return 0;
}
EOF;
        $y=<<<EOF
/* C++ implementation of QuickSort */
#include <bits/stdc++.h>
using namespace std;
 
// A utility function to swap two elements
void swap(int* a, int* b)
{
    int t = *a;
    *a = *b;
    *b = t;
}
 
/* This function takes last element as pivot, places
the pivot element at its correct position in sorted
array, and places all smaller (smaller than pivot)
to left of pivot and all greater elements to right
of pivot */
int partition (int arr[], int low, int high)
{
    int pivot = arr[high]; // pivot
    int i = (low - 1); // Index of smaller element and indicates the right position of pivot found so far
 
    for (int j = low; j <= high - 1; j++)
    {
        // If current element is smaller than the pivot
        if (arr[j] < pivot)
        {
            i++; // increment index of smaller element
            swap(&arr[i], &arr[j]);
        }
    }
    swap(&arr[i + 1], &arr[high]);
    return (i + 1);
}
 
/* The main function that implements QuickSort
arr[] --> Array to be sorted,
low --> Starting index,
high --> Ending index */
void quickSort(int arr[], int low, int high)
{
    if (low < high)
    {
        /* pi is partitioning index, arr[p] is now
        at right place */
        int pi = partition(arr, low, high);
 
        // Separately sort elements before
        // partition and after partition
        quickSort(arr, low, pi - 1);
        quickSort(arr, pi + 1, high);
    }
}
 
/* Function to print an array */
void printArray(int arr[], int size)
{
    int i;
    for (i = 0; i < size; i++)
        cout << arr[i] << " ";
    cout << endl;
}
 
// Driver Code
int main()
{
    int arr[] = {10, 7, 8, 9, 1, 5};
    int n = sizeof(arr) / sizeof(arr[0]);
    quickSort(arr, 0, n - 1);
    cout << "Sorted array: \n";
    printArray(arr, n);
    return 0;
}
EOF;


        return $this->render('courses/sorting_and_searching.html.twig',[
            "code"=>$x,
            "code2"=>$y
        ]);

    }

    /**
     * @Route("/complexity",name="complexity")
     */
    public function complexity(): Response
    {
        return $this->render('courses/complexity.html.twig');

    }
    /**
     * @Route("/data-structure",name="ds")
     */
    public function ds(): Response
    {
        return $this->render('courses/ds.html.twig');

    }
    /**
     * @Route("/tree-data-structure",name="tree")
     */
    public function tree(): Response
    {
        return $this->render('courses/tree.html.twig');

    }

    /**
     * @Route("/dynamic-programming",name="dp")
     */
    public function dp(): Response
    {
        $code1=<<<EOF
using namespace std;

#include <iostream>

class Fibonacci {

public:
  virtual int CalculateFibonacci(int n) {
    if (n < 2) {
      return n;
    }
    return CalculateFibonacci(n - 1) + CalculateFibonacci(n - 2);
  }
};

int main(int argc, char *argv[]) {
  Fibonacci *fib = new Fibonacci();
  cout << "5th Fibonacci is ---> " << fib->CalculateFibonacci(5) << endl;
  cout << "6th Fibonacci is ---> " << fib->CalculateFibonacci(6) << endl;
  cout << "7th Fibonacci is ---> " << fib->CalculateFibonacci(7) << endl;

  delete fib;
}
EOF;
        $code2=<<<EOF
using namespace std;

#include <iostream>
#include <vector>

class Fibonacci {

public:
  virtual int CalculateFibonacci(int n) {
    vector<int> memoize(n + 1, 0);
    return CalculateFibonacciRecursive(memoize, n);
  }

  virtual int CalculateFibonacciRecursive(vector<int> &memoize, int n) {
    if (n < 2) {
      return n;
    }
     // if we have already solved this subproblem, simply return the result from the cache
    if(memoize[n] != 0)
      return memoize[n];
      
    memoize[n] = CalculateFibonacciRecursive(memoize, n - 1) + CalculateFibonacciRecursive(memoize, n - 2);
    return memoize[n];
  }
};

int main(int argc, char *argv[]) {
  Fibonacci *fib = new Fibonacci();
  cout << "5th Fibonacci is ---> " << fib->CalculateFibonacci(5) << endl;
  cout << "6th Fibonacci is ---> " << fib->CalculateFibonacci(6) << endl;
  cout << "7th Fibonacci is ---> " << fib->CalculateFibonacci(7) << endl;

  delete fib;
}
EOF;


        return $this->render('courses/dp.html.twig',[
            "code1"=>$code1,
            "code2"=>$code2
        ]);

    }

    /**
     * @Route("/graph",name="graph")
     */
    public function graph(): Response
    {
        return $this->render('courses/graph.html.twig');

    }
}