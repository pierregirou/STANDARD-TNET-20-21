import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ContenuCompteComponent } from './contenu-compte.component';

describe('ContenuCompteComponent', () => {
  let component: ContenuCompteComponent;
  let fixture: ComponentFixture<ContenuCompteComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ContenuCompteComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ContenuCompteComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
